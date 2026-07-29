# 🔥 NewsVibe - Portal de Noticias de Perú

Una aplicación web moderna y juvenil para consumir noticias en tiempo real desde APIs externas, **enfocada en Perú** con cobertura de política, economía, tecnología, deportes y cultura nacional, además de noticias internacionales relevantes.

![NewsVibe](https://images.unsplash.com/photo-1587595431973-160d0d94add1?w=1200&h=400&fit=crop)

## ✨ Características Principales

### 🎨 Diseño Moderno y Juvenil
- **Gradientes vibrantes** y colores llamativos
- **Animaciones suaves** y efectos hover atractivos
- **Tipografías modernas** (Poppins & Inter de Google Fonts)
- **Modo oscuro** incluido
- **Diseño responsive** para móviles, tablets y desktop

### 📰 Funcionalidades de Noticias
- ✅ **Consumo de APIs reales**: GNews, The Guardian, o modo Demo offline
- ✅ **Enfoque en Perú**: Noticias sobre política peruana, economía nacional, desarrollo tecnológico, deportes peruanos
- ✅ **Verificación de fuentes**: Badge "Verificado" para fuentes confiables (BBC, Reuters, The Guardian)
- ✅ **Categorías**: Tecnología, Deportes, Negocios, Ciencia, Salud, Entretenimiento, Política
- ✅ **Imágenes reales** de Unsplash para todas las noticias
- ✅ **Enlaces funcionales** a artículos reales
- ✅ **Modal de notificaciones**: Muestra las 5 noticias más recientes al hacer clic en el botón de campana

### 👤 Sistema de Usuarios
- 🔐 **Registro y login** local (localStorage)
- 💾 **Guardar noticias** favoritas (límite: 20 gratis, ilimitado Premium)
- 🔗 **Compartir enlaces** (copia al portapapeles)
- 🔔 **Notificaciones** (máximo 5 por día)

### 📊 Métricas y Analytics
- ⏱️ **Tiempo promedio** en la página
- 👁️ **Contador de vistas** por artículo y categoría
- 📈 **Dashboard visual** con gráficos de barras
- 💾 **Historial de guardados**

### 🌟 Funciones Premium
- 🌙 **Modo oscuro** activable
- ♾️ **Guardados ilimitados**
- 🎨 **Badge Premium** visible

### 🎯 Recomendaciones Personalizadas
- Basadas en historial de lectura
- Almacenadas localmente
- Actualizadas dinámicamente

### 📢 Publicidad Discreta
- Anuncios no invasivos
- Diseño integrado
- Relevantes al contenido

---

## 🚀 Cómo Usar

### Opción 1: Abrir directamente (Recomendado para pruebas rápidas)

1. **Abre el archivo** `index.html` en tu navegador favorito (Chrome, Firefox, Edge, Safari)
2. ¡Listo! La aplicación funciona en **modo Demo** con noticias de ejemplo

### Opción 2: Servidor local (Recomendado para APIs externas)

Si quieres usar APIs reales (GNews o The Guardian), necesitas un servidor local para evitar problemas de CORS:

#### Con Python:
```bash
# Python 3
python -m http.server 8000

# Python 2
python -m SimpleHTTPServer 8000
```

#### Con Node.js (npx):
```bash
npx serve
```

#### Con VS Code:
- Instala la extensión **"Live Server"**
- Click derecho en `index.html` → "Open with Live Server"

Luego abre: `http://localhost:8000` (o el puerto que indique tu servidor)

---

## 🔑 Configurar API Keys

### 1. GNews (Recomendado)

1. Ve a [gnews.io](https://gnews.io/) y regístrate gratis
2. Copia tu API Key
3. En NewsVibe, haz clic en **"Configurar"** o **"Ajustes"**
4. Selecciona **"GNews"** como proveedor
5. Pega tu API Key
6. Click en **"Guardar"**

**Plan gratuito**: 100 requests/día

### 2. The Guardian

1. Ve a [open-platform.theguardian.com](https://open-platform.theguardian.com/access/) y regístrate
2. Copia tu API Key
3. En NewsVibe, selecciona **"The Guardian"** como proveedor
4. Pega tu API Key
5. Click en **"Guardar"**

**Plan gratuito**: 500 requests/día

### 3. Modo Demo (Sin API Key) - **Enfoque Perú**

- Funciona **offline**
- **25+ noticias de ejemplo** sobre Perú con URLs e imágenes reales:
  - 🏛️ **Política**: Reformas del Congreso, inversión en infraestructura, relaciones diplomáticas, lucha anticorrupción
  - 💻 **Tecnología**: Startups peruanas, transformación digital, hub tecnológico de Lima
  - ⚽ **Deportes**: Selección peruana, surf, atletas nacionales
  - 💼 **Negocios**: Economía peruana, exportaciones, gastronomía, turismo
  - 🔬 **Ciencia**: Descubrimientos en la Amazonía, energía solar en los Andes
  - 🏥 **Salud**: Telemedicina rural, plantas medicinales ancestrales
  - 🎬 **Entretenimiento**: Cine peruano, música nacional
  - 🌍 **General**: Machu Picchu, gastronomía, cultura
- Perfecto para probar la interfaz sin conexión

---

## 📁 Estructura del Proyecto

```
proyecto_grupo8_interaccion_humano_computador/
│
├── index.html              # Página principal
├── dashboard.html          # Dashboard de métricas
├── README.md              # Este archivo
│
├── css/
│   └── styles.css         # Estilos personalizados modernos
│
└── js/
    ├── auth.js            # Sistema de autenticación
    ├── premium.js         # Funciones premium (modo oscuro, límites)
    ├── metrics.js         # Tracking de métricas
    ├── news.js            # Cliente de APIs de noticias
    └── main.js            # Lógica principal y renderizado
```

---

## 🎮 Guía de Uso Rápido

### 1️⃣ Registro de Usuario

1. En la barra superior derecha, ingresa:
   - **Usuario**: tu nombre de usuario
   - **Contraseña**: tu contraseña
2. Click en **"Registro"**
3. Automáticamente iniciarás sesión

### 2️⃣ Explorar Noticias

1. Selecciona una **categoría** (Tecnología, Deportes, etc.)
2. Selecciona un **proveedor** (Demo, GNews, The Guardian)
3. Click en **"Actualizar"**
4. Navega por las tarjetas de noticias con efectos hover

### 3️⃣ Guardar Noticias

1. **Inicia sesión** primero
2. En cualquier noticia, click en el botón **"Guardar"** (ícono de bookmark)
3. Verás la noticia en el panel lateral **"Guardados"**
4. Límite: **20 noticias** (modo gratuito) o **ilimitado** (Premium)

### 4️⃣ Compartir Noticias

1. **Inicia sesión** primero
2. Click en el botón **"Compartir"** (ícono de compartir)
3. El enlace se copia automáticamente al portapapeles
4. Se registra una notificación

### 5️⃣ Ver Notificaciones (Noticias Recientes)

1. Click en el botón de **campana** (🔔) en la barra superior
2. Se abre un modal con las **5 noticias más recientes**
3. Cada noticia muestra:
   - Imagen miniatura
   - Categoría y badge de verificación
   - Título y descripción
4. Click en cualquier noticia para abrirla en nueva pestaña
5. Se registra automáticamente como notificación vista (máx. 5/día)

### 6️⃣ Activar Premium

1. Click en **"Configurar"** o **"Ajustes"**
2. Activa el switch **"Modo Premium"**
3. Click en **"Guardar"**
4. Ahora tienes:
   - ✨ Badge Premium visible
   - 🌙 Acceso al modo oscuro
   - ♾️ Guardados ilimitados

### 7️⃣ Ver Dashboard

1. Click en **"📊 Stats"** en la barra superior
2. Visualiza:
   - ⏱️ Tiempo promedio en la página
   - 👁️ Total de noticias vistas
   - 💾 Cantidad de guardados
   - 🔔 Notificaciones usadas hoy (máx. 5)
   - 📊 Gráfico de categorías más vistas

---

## 🎨 Personalización

### Cambiar Colores

Edita las variables CSS en `css/styles.css`:

```css
:root {
  --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  --color-primary: #667eea;
  --color-accent: #f5576c;
}
```

### Agregar Más Categorías

Edita `index.html` y `dashboard.html`, añade opciones en el `<select id="categorySelect">`:

```html
<option value="mi-categoria">🎯 Mi Categoría</option>
```

### Cambiar Fuentes Confiables

Edita `js/news.js`, modifica el array `TRUSTED_SOURCES`:

```javascript
const TRUSTED_SOURCES = ['bbc.com','reuters.com','apnews.com','theguardian.com','elpais.com','nytimes.com','tu-fuente.com'];
```

---

## 🛠️ Tecnologías Utilizadas

- **HTML5**: Estructura semántica
- **CSS3**: Gradientes, animaciones, flexbox, grid
- **JavaScript ES6+**: Módulos, async/await, localStorage
- **Bootstrap 5.3**: Grid system y componentes base
- **Bootstrap Icons**: Iconografía moderna
- **Google Fonts**: Poppins & Inter
- **Unsplash**: Imágenes de alta calidad
- **APIs**: GNews, The Guardian

---

## 📱 Compatibilidad

✅ **Navegadores modernos**:
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

✅ **Dispositivos**:
- 💻 Desktop
- 📱 Móviles
- 📲 Tablets

---

## 🔒 Privacidad y Seguridad

- ✅ **Sin backend**: Todo funciona en el navegador
- ✅ **localStorage**: Datos guardados localmente en tu dispositivo
- ✅ **Sin cookies de terceros**
- ✅ **API Keys**: Almacenadas solo en tu navegador
- ⚠️ **Nota**: El hash de contraseña es simple (solo para demo educativa)

---

## 🐛 Solución de Problemas

### Las imágenes no cargan
- **Solución**: Verifica tu conexión a internet. Las imágenes vienen de Unsplash.

### Error de CORS al usar APIs
- **Solución**: Usa un servidor local (ver sección "Cómo Usar")

### Las noticias no se actualizan
- **Solución**: 
  1. Verifica que tu API Key sea válida
  2. Revisa que no hayas excedido el límite de requests
  3. Abre la consola del navegador (F12) para ver errores

### El modo oscuro no funciona
- **Solución**: Activa Premium en Configuración primero

### No puedo guardar noticias
- **Solución**: Debes iniciar sesión primero (Registro/Login)

---

## 📝 Notas Importantes

1. **Modo Demo con enfoque Perú**: Funciona completamente offline con 25+ noticias de ejemplo sobre Perú
2. **Noticias peruanas**: Incluye política nacional, economía, tecnología, deportes y cultura de Perú
3. **APIs Gratuitas**: Tienen límites de requests diarios (100 GNews, 500 Guardian)
4. **localStorage**: Los datos se borran si limpias el caché del navegador
5. **Proyecto Educativo**: Diseñado para estudiantes y desarrolladores peruanos

---

## 🎯 Próximas Mejoras (Roadmap)

- [ ] Integración con NewsAPI.org
- [ ] Sistema de comentarios
- [ ] Compartir en redes sociales
- [ ] Búsqueda de noticias
- [ ] Filtros avanzados
- [ ] PWA (Progressive Web App)
- [ ] Backend real con base de datos
- [ ] Sistema de autenticación OAuth

---

## 👥 Créditos

- **Diseño**: Inspirado en tendencias modernas de UI/UX 2024
- **Imágenes**: [Unsplash](https://unsplash.com)
- **Iconos**: [Bootstrap Icons](https://icons.getbootstrap.com)
- **Fuentes**: [Google Fonts](https://fonts.google.com)
- **APIs**: [GNews](https://gnews.io), [The Guardian](https://open-platform.theguardian.com)

---

## 📄 Licencia

Este es un **proyecto educativo** creado con fines demostrativos.

---

## 💬 Soporte

Si tienes preguntas o encuentras problemas:

1. Revisa la sección **"Solución de Problemas"**
2. Abre la consola del navegador (F12) para ver errores
3. Verifica que todos los archivos estén en las carpetas correctas

---

## 🎉 ¡Disfruta NewsVibe!

Mantente informado con estilo 🚀

---

**Última actualización**: Octubre 2025
