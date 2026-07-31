"""
Sistema de Blog
Gestión de posts y comentarios
"""

from datetime import datetime
from enum import Enum


class Categoria(Enum):
    """Categorías de posts"""
    TECNOLOGIA = "Tecnología"
    PROGRAMACION = "Programación"
    CIENCIA = "Ciencia"
    ARTE = "Arte"
    EDUCACION = "Educación"
    ENTRETENIMIENTO = "Entretenimiento"
    OTRO = "Otro"


class EstadoPost(Enum):
    """Estados posibles de un post"""
    BORRADOR = "Borrador"
    PUBLICADO = "Publicado"
    ARCHIVADO = "Archivado"


class Comentario:
    """Clase que representa un comentario"""
    
    def __init__(self, id_comentario, autor, contenido, post_id):
        self.id_comentario = id_comentario
        self.autor = autor
        self.contenido = contenido
        self.post_id = post_id
        self.fecha_creacion = datetime.now()
        self.activo = True
    
    def __str__(self):
        return f"{self.autor}: {self.contenido[:50]}... ({self.fecha_creacion.strftime('%Y-%m-%d')})"


class Post:
    """Clase que representa un post del blog"""
    
    def __init__(self, id_post, titulo, contenido, autor, categoria):
        self.id_post = id_post
        self.titulo = titulo
        self.contenido = contenido
        self.autor = autor
        self.categoria = categoria
        self.estado = EstadoPost.BORRADOR
        self.fecha_creacion = datetime.now()
        self.fecha_publicacion = None
        self.fecha_actualizacion = datetime.now()
        self.comentarios = []
        self.etiquetas = []
        self.vistas = 0
    
    def publicar(self):
        """Publica el post"""
        if self.estado == EstadoPost.BORRADOR:
            self.estado = EstadoPost.PUBLICADO
            self.fecha_publicacion = datetime.now()
            return True
        return False
    
    def archivar(self):
        """Archiva el post"""
        self.estado = EstadoPost.ARCHIVADO
        return True
    
    def agregar_comentario(self, comentario):
        """Agrega un comentario al post"""
        self.comentarios.append(comentario)
    
    def agregar_etiqueta(self, etiqueta):
        """Agrega una etiqueta al post"""
        if etiqueta not in self.etiquetas:
            self.etiquetas.append(etiqueta)
    
    def eliminar_etiqueta(self, etiqueta):
        """Elimina una etiqueta del post"""
        if etiqueta in self.etiquetas:
            self.etiquetas.remove(etiqueta)
    
    @property
    def cantidad_comentarios(self):
        """Retorna la cantidad de comentarios activos"""
        return sum(1 for c in self.comentarios if c.activo)
    
    @property
    def resumen(self):
        """Retorna un resumen del contenido"""
        return self.contenido[:100] + "..." if len(self.contenido) > 100 else self.contenido
    
    def incrementar_vistas(self):
        """Incrementa el contador de vistas"""
        self.vistas += 1
    
    def __str__(self):
        return f"{self.id_post} - {self.titulo} - {self.autor} - {self.estado.value}"


class SistemaBlog:
    """Clase que gestiona el sistema de blog"""
    
    def __init__(self, nombre_blog):
        self.nombre_blog = nombre_blog
        self.posts = {}
        self.contador_posts = 1
        self.contador_comentarios = 1
    
    def crear_post(self, titulo, contenido, autor, categoria):
        """Crea un nuevo post"""
        id_post = f"POST-{str(self.contador_posts).zfill(4)}"
        post = Post(id_post, titulo, contenido, autor, categoria)
        self.posts[id_post] = post
        self.contador_posts += 1
        return post
    
    def obtener_post(self, id_post):
        """Obtiene un post por su ID"""
        post = self.posts.get(id_post)
        if post:
            post.incrementar_vistas()
        return post
    
    def actualizar_post(self, id_post, titulo=None, contenido=None, categoria=None):
        """Actualiza un post existente"""
        post = self.obtener_post(id_post)
        if post:
            if titulo:
                post.titulo = titulo
            if contenido:
                post.contenido = contenido
            if categoria:
                post.categoria = categoria
            post.fecha_actualizacion = datetime.now()
            return True
        return False
    
    def eliminar_post(self, id_post):
        """Elimina un post"""
        if id_post in self.posts:
            del self.posts[id_post]
            return True
        return False
    
    def publicar_post(self, id_post):
        """Publica un post"""
        post = self.obtener_post(id_post)
        if post:
            return post.publicar()
        return False
    
    def archivar_post(self, id_post):
        """Archiva un post"""
        post = self.obtener_post(id_post)
        if post:
            return post.archivar()
        return False
    
    def listar_posts(self, estado=None, categoria=None, autor=None):
        """Lista posts con filtros opcionales"""
        posts_filtrados = list(self.posts.values())
        
        if estado:
            posts_filtrados = [p for p in posts_filtrados if p.estado == estado]
        
        if categoria:
            posts_filtrados = [p for p in posts_filtrados if p.categoria == categoria]
        
        if autor:
            posts_filtrados = [p for p in posts_filtrados if p.autor.lower() == autor.lower()]
        
        return sorted(posts_filtrados, key=lambda x: x.fecha_creacion, reverse=True)
    
    def buscar_posts(self, query):
        """Busca posts por título, contenido o etiquetas"""
        query = query.lower()
        resultados = []
        
        for post in self.posts.values():
            if (query in post.titulo.lower() or 
                query in post.contenido.lower() or 
                any(query in etiqueta.lower() for etiqueta in post.etiquetas)):
                resultados.append(post)
        
        return sorted(resultados, key=lambda x: x.fecha_creacion, reverse=True)
    
    def agregar_comentario(self, post_id, autor, contenido):
        """Agrega un comentario a un post"""
        post = self.obtener_post(post_id)
        if post:
            id_comentario = f"COM-{str(self.contador_comentarios).zfill(4)}"
            comentario = Comentario(id_comentario, autor, contenido, post_id)
            post.agregar_comentario(comentario)
            self.contador_comentarios += 1
            return comentario
        return None
    
    def eliminar_comentario(self, post_id, comentario_id):
        """Elimina (desactiva) un comentario"""
        post = self.obtener_post(post_id)
        if post:
            for comentario in post.comentarios:
                if comentario.id_comentario == comentario_id:
                    comentario.activo = False
                    return True
        return False
    
    def listar_comentarios_post(self, post_id):
        """Lista los comentarios de un post"""
        post = self.obtener_post(post_id)
        if post:
            return [c for c in post.comentarios if c.activo]
        return []
    
    def posts_mas_vistos(self, limite=5):
        """Retorna los posts más vistos"""
        posts_ordenados = sorted(self.posts.values(), key=lambda x: x.vistas, reverse=True)
        return posts_ordenados[:limite]
    
    def posts_recientes(self, limite=5):
        """Retorna los posts más recientes"""
        posts_ordenados = sorted(self.posts.values(), key=lambda x: x.fecha_creacion, reverse=True)
        return posts_ordenados[:limite]
    
    def estadisticas(self):
        """Genera estadísticas del blog"""
        total = len(self.posts)
        por_estado = {}
        por_categoria = {}
        total_comentarios = 0
        total_vistas = 0
        
        for post in self.posts.values():
            # Por estado
            estado = post.estado.value
            por_estado[estado] = por_estado.get(estado, 0) + 1
            
            # Por categoría
            categoria = post.categoria.value
            por_categoria[categoria] = por_categoria.get(categoria, 0) + 1
            
            # Comentarios y vistas
            total_comentarios += post.cantidad_comentarios
            total_vistas += post.vistas
        
        return {
            'total_posts': total,
            'por_estado': por_estado,
            'por_categoria': por_categoria,
            'total_comentarios': total_comentarios,
            'total_vistas': total_vistas
        }


def limpiar_pantalla():
    """Limpia la pantalla de la consola"""
    import os
    os.system('cls' if os.name == 'nt' else 'clear')


def mostrar_menu():
    """Muestra el menú principal"""
    print("\n" + "=" * 60)
    print("   SISTEMA DE BLOG")
    print("=" * 60)
    print("1. Crear post")
    print("2. Ver post por ID")
    print("3. Listar todos los posts")
    print("4. Listar posts publicados")
    print("5. Listar posts por categoría")
    print("6. Listar posts por autor")
    print("7. Buscar posts")
    print("8. Publicar post")
    print("9. Archivar post")
    print("10. Actualizar post")
    print("11. Eliminar post")
    print("12. Agregar comentario")
    print("13. Ver comentarios de un post")
    print("14. Posts más vistos")
    print("15. Posts recientes")
    print("16. Ver estadísticas")
    print("0. Salir")
    print("=" * 60)


def mostrar_categorias():
    """Muestra las categorías disponibles"""
    print("\nCategorías disponibles:")
    for i, categoria in enumerate(Categoria, 1):
        print(f"{i}. {categoria.value}")


def main():
    """Función principal del sistema de blog"""
    print("=== SISTEMA DE BLOG ===")
    nombre = input("Nombre del blog: ")
    sistema = SistemaBlog(nombre)
    
    print(f"\nBlog '{nombre}' configurado.")
    
    while True:
        mostrar_menu()
        
        try:
            opcion = int(input("\nSeleccione una opción (0-16): "))
            
            if opcion == 0:
                print("\n¡Gracias por usar el Sistema de Blog! ¡Hasta pronto!")
                break
            
            elif opcion == 1:
                # Crear post
                print("\n--- Crear Nuevo Post ---")
                titulo = input("Título: ")
                contenido = input("Contenido: ")
                autor = input("Autor: ")
                
                mostrar_categorias()
                idx = int(input("Seleccione categoría (número): ")) - 1
                
                categorias = list(Categoria)
                if 0 <= idx < len(categorias):
                    categoria = categorias[idx]
                else:
                    print("Categoría inválida.")
                    continue
                
                post = sistema.crear_post(titulo, contenido, autor, categoria)
                print(f"\n¡Post creado exitosamente!")
                print(f"ID: {post.id_post}")
                print(f"Estado: {post.estado.value}")
            
            elif opcion == 2:
                # Ver por ID
                print("\n--- Ver Post por ID ---")
                id_post = input("ID del post: ")
                post = sistema.obtener_post(id_post)
                
                if post:
                    print(f"\nID: {post.id_post}")
                    print(f"Título: {post.titulo}")
                    print(f"Autor: {post.autor}")
                    print(f"Categoría: {post.categoria.value}")
                    print(f"Estado: {post.estado.value}")
                    print(f"Fecha creación: {post.fecha_creacion.strftime('%Y-%m-%d %H:%M:%S')}")
                    if post.fecha_publicacion:
                        print(f"Fecha publicación: {post.fecha_publicacion.strftime('%Y-%m-%d %H:%M:%S')}")
                    print(f"Vistas: {post.vistas}")
                    print(f"Comentarios: {post.cantidad_comentarios}")
                    if post.etiquetas:
                        print(f"Etiquetas: {', '.join(post.etiquetas)}")
                    print(f"\nContenido:\n{post.contenido}")
                else:
                    print("Post no encontrado.")
            
            elif opcion == 3:
                # Listar todos
                print("\n--- Todos los Posts ---")
                posts = sistema.listar_posts()
                if posts:
                    for post in posts:
                        print(f"{post.id_post} - {post.titulo} - {post.autor} - {post.estado.value} - {post.vistas} vistas")
                else:
                    print("No hay posts registrados.")
            
            elif opcion == 4:
                # Listar publicados
                print("\n--- Posts Publicados ---")
                posts = sistema.listar_posts(estado=EstadoPost.PUBLICADO)
                if posts:
                    for post in posts:
                        print(f"{post.id_post} - {post.titulo} - {post.autor} - {post.fecha_publicacion.strftime('%Y-%m-%d')}")
                else:
                    print("No hay posts publicados.")
            
            elif opcion == 5:
                # Listar por categoría
                print("\n--- Posts por Categoría ---")
                mostrar_categorias()
                idx = int(input("Seleccione categoría (número): ")) - 1
                
                categorias = list(Categoria)
                if 0 <= idx < len(categorias):
                    categoria = categorias[idx]
                    posts = sistema.listar_posts(categoria=categoria)
                    if posts:
                        for post in posts:
                            print(f"{post.id_post} - {post.titulo} - {post.autor} - {post.estado.value}")
                    else:
                        print(f"No hay posts en la categoría {categoria.value}.")
                else:
                    print("Categoría inválida.")
            
            elif opcion == 6:
                # Listar por autor
                print("\n--- Posts por Autor ---")
                autor = input("Nombre del autor: ")
                posts = sistema.listar_posts(autor=autor)
                if posts:
                    for post in posts:
                        print(f"{post.id_post} - {post.titulo} - {post.fecha_creacion.strftime('%Y-%m-%d')} - {post.estado.value}")
                else:
                    print(f"No hay posts de {autor}.")
            
            elif opcion == 7:
                # Buscar
                print("\n--- Buscar Posts ---")
                query = input("Término de búsqueda: ")
                posts = sistema.buscar_posts(query)
                if posts:
                    for post in posts:
                        print(f"{post.id_post} - {post.titulo} - {post.resumen}")
                else:
                    print("No se encontraron posts.")
            
            elif opcion == 8:
                # Publicar
                print("\n--- Publicar Post ---")
                id_post = input("ID del post: ")
                if sistema.publicar_post(id_post):
                    print("Post publicado exitosamente.")
                else:
                    print("No se pudo publicar el post.")
            
            elif opcion == 9:
                # Archivar
                print("\n--- Archivar Post ---")
                id_post = input("ID del post: ")
                if sistema.archivar_post(id_post):
                    print("Post archivado exitosamente.")
                else:
                    print("No se pudo archivar el post.")
            
            elif opcion == 10:
                # Actualizar
                print("\n--- Actualizar Post ---")
                id_post = input("ID del post: ")
                post = sistema.obtener_post(id_post)
                
                if post:
                    print("Deje en blanco para mantener el valor actual")
                    titulo = input(f"Título [{post.titulo}]: ") or None
                    contenido = input(f"Contenido [{post.contenido[:30]}...]: ") or None
                    
                    mostrar_categorias()
                    idx = input(f"Categoría [{post.categoria.value}] (número o Enter): ")
                    categoria = None
                    if idx:
                        idx_num = int(idx) - 1
                        categorias = list(Categoria)
                        if 0 <= idx_num < len(categorias):
                            categoria = categorias[idx_num]
                    
                    if sistema.actualizar_post(id_post, titulo, contenido, categoria):
                        print("Post actualizado exitosamente.")
                else:
                    print("Post no encontrado.")
            
            elif opcion == 11:
                # Eliminar
                print("\n--- Eliminar Post ---")
                id_post = input("ID del post: ")
                confirmacion = input("¿Está seguro de eliminar este post? (s/n): ")
                if confirmacion.lower() == 's':
                    if sistema.eliminar_post(id_post):
                        print("Post eliminado exitosamente.")
                    else:
                        print("No se pudo eliminar el post.")
                else:
                    print("Operación cancelada.")
            
            elif opcion == 12:
                # Agregar comentario
                print("\n--- Agregar Comentario ---")
                id_post = input("ID del post: ")
                autor = input("Autor del comentario: ")
                contenido = input("Contenido del comentario: ")
                
                comentario = sistema.agregar_comentario(id_post, autor, contenido)
                if comentario:
                    print("Comentario agregado exitosamente.")
                else:
                    print("No se pudo agregar el comentario.")
            
            elif opcion == 13:
                # Ver comentarios
                print("\n--- Comentarios del Post ---")
                id_post = input("ID del post: ")
                comentarios = sistema.listar_comentarios_post(id_post)
                if comentarios:
                    for comentario in comentarios:
                        print(f"{comentario.id_comentario} - {comentario.autor}")
                        print(f"  {comentario.contenido}")
                        print(f"  {comentario.fecha_creacion.strftime('%Y-%m-%d %H:%M:%S')}")
                        print("-" * 40)
                else:
                    print("No hay comentarios para este post.")
            
            elif opcion == 14:
                # Más vistos
                print("\n--- Posts Más Vistos ---")
                posts = sistema.posts_mas_vistos()
                if posts:
                    for i, post in enumerate(posts, 1):
                        print(f"{i}. {post.titulo} - {post.vistas} vistas - {post.autor}")
                else:
                    print("No hay posts registrados.")
            
            elif opcion == 15:
                # Recientes
                print("\n--- Posts Recientes ---")
                posts = sistema.posts_recientes()
                if posts:
                    for i, post in enumerate(posts, 1):
                        print(f"{i}. {post.titulo} - {post.fecha_creacion.strftime('%Y-%m-%d')} - {post.autor}")
                else:
                    print("No hay posts registrados.")
            
            elif opcion == 16:
                # Estadísticas
                stats = sistema.estadisticas()
                print("\n--- Estadísticas ---")
                print(f"Total de posts: {stats['total_posts']}")
                print(f"Total de comentarios: {stats['total_comentarios']}")
                print(f"Total de vistas: {stats['total_vistas']}")
                print("\nPor estado:")
                for estado, cantidad in stats['por_estado'].items():
                    print(f"  {estado}: {cantidad}")
                print("\nPor categoría:")
                for categoria, cantidad in stats['por_categoria'].items():
                    print(f"  {categoria}: {cantidad}")
            
            else:
                print("\nOpción no válida. Por favor seleccione una opción entre 0 y 16.")
            
            input("\nPresione Enter para continuar...")
            limpiar_pantalla()
        
        except ValueError:
            print("\nError: Por favor ingrese un número válido.")
            input("Presione Enter para continuar...")
            limpiar_pantalla()


if __name__ == "__main__":
    main()
