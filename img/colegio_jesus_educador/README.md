# Colegio Jesús Educador - Sitio Web Oficial

Una página web moderna, profesional y totalmente responsive para el Colegio Jesús Educador, diseñada para showcasear la excelencia educativa y las actividades del colegio.

## 🎯 Objetivos del Sitio Web

- Mostrar la educación de calidad que brinda el colegio
- Presentar el ambiente escolar positivo
- Exhibir actividades recreativas y deportivas
- Compartir experiencias de paseos educativos
- Destacar la formación integral de los estudiantes

## 📁 Estructura del Proyecto

```
colegio_jesus_educador/
├── index.html              # Página principal
├── styles.css              # Estilos CSS
├── script.js               # Funcionalidad JavaScript
├── README.md               # Documentación
├── imagen_logo/            # Imágenes del logo y fondo
│   ├── logo.jpg           # Logo del colegio
│   └── fondo.jpg          # Imagen principal de fondo
├── actividades_escolares/   # Galería de actividades escolares
│   ├── actividad1.jpg
│   ├── actividad2.jpg
│   └── ...
├── imganes_paseo/          # Galería de paseos educativos
│   ├── paseo1.jpg
│   ├── paseo2.jpg
│   └── ...
└── olimpiadas/             # Galería de olimpiadas deportivas
    ├── olimpiada1.jpg
    ├── olimpiada2.jpg
    └── ...
```

## 🚀 Características Principales

### 📱 Responsive Design
- **Mobile-first**: Diseñado para funcionar perfectamente en celulares
- **Tablet Optimized**: Experiencia fluida en tablets
- **Desktop Ready**: Aprovecha el espacio en pantallas grandes
- **Flexible Grid**: Sistema de cuadrícula adaptable

### 🎨 Diseño Moderno
- **Esquema de Color Profesional**: Azul, blanco y dorado
- **Tipografía Moderna**: Montserrat y Open Sans
- **Animaciones Suaves**: Transiciones elegantes y micro-interacciones
- **Sombras Elegantes**: Efectos de profundidad profesionales

### 📋 Secciones del Sitio

#### 1. **Header Navegación**
- Logo del colegio
- Menú responsive con menú hamburguesa para móviles
- Navegación suave entre secciones
- Efecto de scroll que cambia la apariencia

#### 2. **Hero Section**
- Imagen de fondo impactante
- Texto llamativo con mensaje principal
- Botones de acción
- Overlay oscuro para mejor legibilidad

#### 3. **Nosotros**
- Misión y valores del colegio
- Cuatro pilares fundamentales:
  - Excelencia Académica
  - Valores Humanos
  - Desarrollo Integral
  - Disciplina

#### 4. **Galerías de Imágenes**
- **Actividades Escolares**: 11 imágenes automáticamente cargadas
- **Paseos Educativos**: 9 imágenes de experiencias fuera del aula
- **Olimpiadas Deportivas**: 12 imágenes de competencias deportivas
- Lightbox integrado para visualización ampliada

#### 5. **Contacto**
- Información de contacto completa
- Formulario funcional con validación
- Diseño en dos columnas (desktop) / una columna (móvil)

#### 6. **Footer**
- Logo del colegio
- Derechos reservados
- Enlaces a redes sociales

### 📱 Características Especiales

#### Botón de WhatsApp Flotante
- Posicionado fijo en la esquina inferior derecha
- Animación de pulso constante
- Abre WhatsApp con mensaje predefinido
- Funciona en todos los dispositivos

#### Galerías Automáticas
- Carga dinámica de imágenes desde carpetas
- Lazy loading para optimización
- Efectos hover elegantes
- Lightbox para visualización completa

#### Formulario de Contacto
- Validación en tiempo real
- Notificaciones de éxito/error
- Diseño moderno y accesible

## 🛠️ Tecnologías Utilizadas

- **HTML5**: Semántico y accesible
- **CSS3**: Flexbox, Grid, Animaciones, Media Queries
- **JavaScript Vanilla**: ES6+, Async/Await, APIs modernas
- **Font Awesome**: Iconos profesionales
- **Google Fonts**: Montserrat y Open Sans

## 📐 Breakpoints Responsive

- **Mobile**: < 480px
- **Tablet**: 481px - 768px
- **Desktop**: 769px - 1399px
- **Large Desktop**: > 1400px

## 🎯 Optimizaciones

### Performance
- Lazy loading de imágenes
- CSS y JavaScript optimizados
- Animaciones con GPU acceleration
- Preloader para mejor experiencia

### SEO
- Estructura semántica HTML5
- Meta tags optimizados
- Navegación interna con anchors
- Imágenes con alt text descriptivo

### UX/UI
- Navegación intuitiva
- Feedback visual en interacciones
- Transiciones suaves
- Diseño consistente

## 🚀 Cómo Usar

1. **Abrir el Sitio**: Simplemente abre `index.html` en tu navegador
2. **Navegación**: Usa el menú principal o el menú hamburguesa en móviles
3. **Galerías**: Haz clic en cualquier imagen para verla en tamaño completo
4. **Contacto**: Llena el formulario para enviar un mensaje
5. **WhatsApp**: Presiona el botón flotante para contactar directamente

## 📝 Personalización

### Cambiar Colores
Edita las variables CSS en `styles.css`:
```css
:root {
    --primary-color: #1e3a8a;
    --secondary-color: #fbbf24;
    /* ... otras variables */
}
```

### Agregar Nuevas Imágenes
1. Añade las imágenes a las carpetas correspondientes
2. Actualiza los arrays en `script.js` si es necesario
3. Las galerías se actualizarán automáticamente

### Modificar Texto
Edita el contenido directamente en `index.html` o usa JavaScript para contenido dinámico.

## 🌐 Compatibilidad del Navegador

- ✅ Chrome 60+
- ✅ Firefox 55+
- ✅ Safari 12+
- ✅ Edge 79+
- ✅ iOS Safari 12+
- ✅ Android Chrome 60+

## 📞 Información de Contacto

Para personalizar la información de contacto, edita la sección correspondiente en `index.html`:

```html
<div class="contacto-item">
    <div class="contacto-icon">
        <i class="fas fa-map-marker-alt"></i>
    </div>
    <div class="contacto-text">
        <h3>Dirección</h3>
        <p>[Tu dirección aquí]</p>
    </div>
</div>
```

## 🔧 Configuración del Botón WhatsApp

Para configurar el número de WhatsApp, edita el enlace en `index.html`:

```html
<a href="https://wa.me/TUNUMERO?text=Hola%20buenos%20d%C3%ADas..." class="whatsapp-btn">
```

Reemplaza `TUNUMERO` con tu número de teléfono (incluyendo código de país sin +).

## 📄 Licencia

© 2026 Colegio Jesús Educador - Todos los derechos reservados

---

**Desarrollado con ❤️ para Colegio Jesús Educador**
