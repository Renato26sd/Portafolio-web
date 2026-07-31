from flask import Flask, jsonify, request
from flask_cors import CORS
from flask_sqlalchemy import SQLAlchemy
from datetime import datetime
import os

app = Flask(__name__)
CORS(app)

# Database configuration
basedir = os.path.abspath(os.path.dirname(__file__))
app.config['SQLALCHEMY_DATABASE_URI'] = 'sqlite:///' + os.path.join(basedir, 'tienda.db')
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

db = SQLAlchemy(app)

# Models
class Product(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(100), nullable=False)
    description = db.Column(db.Text)
    price = db.Column(db.Float, nullable=False)
    stock = db.Column(db.Integer, default=0)
    image_url = db.Column(db.String(200))
    category = db.Column(db.String(50))
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    
    def to_dict(self):
        return {
            'id': self.id,
            'name': self.name,
            'description': self.description,
            'price': self.price,
            'stock': self.stock,
            'image_url': self.image_url,
            'category': self.category,
            'created_at': self.created_at.isoformat()
        }

class Order(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    customer_name = db.Column(db.String(100), nullable=False)
    customer_email = db.Column(db.String(100), nullable=False)
    total = db.Column(db.Float, nullable=False)
    status = db.Column(db.String(20), default='pending')
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    
    items = db.relationship('OrderItem', backref='order', lazy=True, cascade='all, delete-orphan')
    
    def to_dict(self):
        return {
            'id': self.id,
            'customer_name': self.customer_name,
            'customer_email': self.customer_email,
            'total': self.total,
            'status': self.status,
            'created_at': self.created_at.isoformat(),
            'items': [item.to_dict() for item in self.items]
        }

class OrderItem(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    order_id = db.Column(db.Integer, db.ForeignKey('order.id'), nullable=False)
    product_id = db.Column(db.Integer, db.ForeignKey('product.id'), nullable=False)
    quantity = db.Column(db.Integer, nullable=False)
    price = db.Column(db.Float, nullable=False)
    
    product = db.relationship('Product')
    
    def to_dict(self):
        return {
            'id': self.id,
            'product_id': self.product_id,
            'product_name': self.product.name if self.product else 'Unknown',
            'quantity': self.quantity,
            'price': self.price,
            'subtotal': self.quantity * self.price
        }

# Routes
@app.route('/api/products', methods=['GET'])
def get_products():
    products = Product.query.all()
    return jsonify([p.to_dict() for p in products])

@app.route('/api/products/<int:product_id>', methods=['GET'])
def get_product(product_id):
    product = Product.query.get_or_404(product_id)
    return jsonify(product.to_dict())

@app.route('/api/products', methods=['POST'])
def create_product():
    data = request.json
    product = Product(
        name=data['name'],
        description=data.get('description', ''),
        price=data['price'],
        stock=data.get('stock', 0),
        image_url=data.get('image_url', ''),
        category=data.get('category', 'general')
    )
    db.session.add(product)
    db.session.commit()
    return jsonify(product.to_dict()), 201

@app.route('/api/products/<int:product_id>', methods=['PUT'])
def update_product(product_id):
    product = Product.query.get_or_404(product_id)
    data = request.json
    
    product.name = data.get('name', product.name)
    product.description = data.get('description', product.description)
    product.price = data.get('price', product.price)
    product.stock = data.get('stock', product.stock)
    product.image_url = data.get('image_url', product.image_url)
    product.category = data.get('category', product.category)
    
    db.session.commit()
    return jsonify(product.to_dict())

@app.route('/api/products/<int:product_id>', methods=['DELETE'])
def delete_product(product_id):
    product = Product.query.get_or_404(product_id)
    db.session.delete(product)
    db.session.commit()
    return jsonify({'message': 'Product deleted'})

@app.route('/api/orders', methods=['GET'])
def get_orders():
    orders = Order.query.order_by(Order.created_at.desc()).all()
    return jsonify([o.to_dict() for o in orders])

@app.route('/api/orders/<int:order_id>', methods=['GET'])
def get_order(order_id):
    order = Order.query.get_or_404(order_id)
    return jsonify(order.to_dict())

@app.route('/api/orders', methods=['POST'])
def create_order():
    data = request.json
    
    order = Order(
        customer_name=data['customer_name'],
        customer_email=data['customer_email'],
        total=data['total']
    )
    
    for item_data in data['items']:
        product = Product.query.get(item_data['product_id'])
        if product:
            order_item = OrderItem(
                product_id=product.id,
                quantity=item_data['quantity'],
                price=product.price
            )
            order.items.append(order_item)
            
            # Update stock
            product.stock -= item_data['quantity']
    
    db.session.add(order)
    db.session.commit()
    return jsonify(order.to_dict()), 201

@app.route('/api/orders/<int:order_id>/status', methods=['PUT'])
def update_order_status(order_id):
    order = Order.query.get_or_404(order_id)
    data = request.json
    order.status = data['status']
    db.session.commit()
    return jsonify(order.to_dict())

@app.route('/api/stats', methods=['GET'])
def get_stats():
    total_products = Product.query.count()
    total_orders = Order.query.count()
    total_revenue = db.session.query(db.func.sum(Order.total)).filter(Order.status == 'completed').scalar() or 0
    pending_orders = Order.query.filter_by(status='pending').count()
    
    return jsonify({
        'total_products': total_products,
        'total_orders': total_orders,
        'total_revenue': total_revenue,
        'pending_orders': pending_orders
    })

# Initialize database
@app.before_request
def create_tables():
    if not hasattr(app, '_tables_created'):
        db.create_all()
        
        # Add sample products if none exist
        if Product.query.count() == 0:
            sample_products = [
                Product(name='Laptop HP', description='Laptop HP 15.6" Intel Core i5', price=899.99, stock=10, category='Electrónica'),
                Product(name='Mouse Inalámbrico', description='Mouse inalámbrico ergonómico', price=29.99, stock=50, category='Accesorios'),
                Product(name='Teclado Mecánico', description='Teclado mecánico RGB', price=79.99, stock=25, category='Accesorios'),
                Product(name='Monitor 24"', description='Monitor LED 24" Full HD', price=199.99, stock=15, category='Electrónica'),
                Product(name='Auriculares Bluetooth', description='Auriculares inalámbricos con cancelación de ruido', price=149.99, stock=30, category='Audio'),
                Product(name='Cámara Web HD', description='Cámara web 1080p con micrófono', price=59.99, stock=20, category='Electrónica'),
                Product(name='Disco SSD 1TB', description='Disco sólido SSD 1TB SATA', price=89.99, stock=35, category='Almacenamiento'),
                Product(name='Memoria RAM 16GB', description='Memoria DDR4 16GB 3200MHz', price=69.99, stock=40, category='Componentes'),
            ]
            db.session.add_all(sample_products)
            db.session.commit()
        
        app._tables_created = True

if __name__ == '__main__':
    app.run(debug=True, port=5000)
