(function(){
  const LS_SETTINGS = 'np_settings';
  const TRUSTED_SOURCES = ['bbc.com','reuters.com','apnews.com','theguardian.com','elpais.com','nytimes.com'];

  function getSettings(){
    return JSON.parse(localStorage.getItem(LS_SETTINGS) || '{"provider":"demo"}');
  }
  function saveSettings(s){ localStorage.setItem(LS_SETTINGS, JSON.stringify(s)); }

  function setProvider(provider){
    const s = getSettings(); s.provider = provider; saveSettings(s);
  }
  function setApiKey(apiKey){ const s = getSettings(); s.apiKey = apiKey; saveSettings(s); }

  function sourceHost(url){ try{ return new URL(url).host.replace('www.',''); }catch{ return ''; } }
  function isTrusted(url){ return TRUSTED_SOURCES.some(ts => sourceHost(url).includes(ts)); }

  // Mapear categorías al proveedor
  function normalizeCategory(cat){
    const map = { technology:'technology', sports:'sports', business:'business', science:'science', health:'health', entertainment:'entertainment', politics:'politics', general:'general' };
    return map[cat] || 'general';
  }

  // Function to check if a date is within September 11 to November 11, 2025
  function isRecent(publishedAt) {
    if (!publishedAt) return false;
    const startDate = new Date(2025, 8, 11); // September 11, 2025 (months are 0-indexed)
    const endDate = new Date(2025, 10, 11);  // November 11, 2025
    const articleDate = new Date(publishedAt);
    return articleDate >= startDate && articleDate <= endDate;
  }

  async function fetchNews({category='general'}={}){
    const s = getSettings();
    const cat = normalizeCategory(category);

    if(s.provider==='demo' || !s.provider){
      const allNews = demoData(cat);
      // Filter demo data to only include recent news
      const recentNews = allNews.filter(article => isRecent(article.publishedAt));
      
      // If no recent news in demo data, return the latest one with current timestamp
      if (recentNews.length === 0 && allNews.length > 0) {
        allNews[0].publishedAt = new Date().toISOString();
        return [allNews[0]];
      }
      return recentNews;
    }

    if(s.provider==='gnews'){
      if(!s.apiKey) throw new Error('GNews requiere API Key. Configura en Ajustes.');
      // GNews: https://gnews.io/api/v4/top-headlines?category=technology&lang=es&country=mx&max=20&apikey=...
      const url = `https://gnews.io/api/v4/top-headlines?category=${encodeURIComponent(cat)}&lang=es&max=20&apikey=${encodeURIComponent(s.apiKey)}`;
      const res = await fetch(url);
      if(!res.ok) throw new Error('Error GNews: '+res.status);
      const data = await res.json();
      return (data.articles || [])
        .map(a => ({
          id: a.url,
          title: a.title,
          description: a.description || '',
          url: a.url,
          image: a.image || '',
          source: sourceHost(a.source?.name || a.url),
          category: cat,
          publishedAt: a.publishedAt || new Date().toISOString()
        }))
        .filter(article => isRecent(article.publishedAt));
    }

    if(s.provider==='guardian'){
      if(!s.apiKey) throw new Error('The Guardian requiere API Key. Configura en Ajustes.');
      // Guardian: https://content.guardianapis.com/search?section=technology&show-fields=trailText,thumbnail&api-key=...
      const section = cat==='technology'? 'technology' : cat==='sports'? 'sport' : cat==='politics'? 'politics' : cat==='business'? 'business' : cat==='science'? 'science' : cat==='health'? 'society' : cat==='entertainment'? 'culture' : 'news';
      const url = `https://content.guardianapis.com/search?section=${encodeURIComponent(section)}&page-size=20&order-by=newest&show-fields=trailText,thumbnail&api-key=${encodeURIComponent(s.apiKey)}`;
      const res = await fetch(url);
      if(!res.ok) throw new Error('Error Guardian: '+res.status);
      const data = await res.json();
      return (data.response?.results || [])
        .map(a => ({
          id: a.id,
          title: a.webTitle,
          description: a.fields?.trailText?.replace(/<[^>]+>/g,'') || '',
          url: a.webUrl,
          image: a.fields?.thumbnail || '',
          source: 'theguardian.com',
          category: cat,
          publishedAt: a.webPublicationDate || new Date().toISOString()
        }))
        .filter(article => isRecent(article.publishedAt));
    }

    throw new Error('Proveedor no soportado');
  }

  function demoData(cat) {
    // Set fixed dates from September 11 to November 11, 2025
    // Note: JavaScript months are 0-indexed (0=January, 10=November)
    const dates = {
      nov11: new Date(2025, 10, 11).toISOString(),  // 11 de noviembre 2025
      nov10: new Date(2025, 10, 10).toISOString(),  // 10 de noviembre 2025
      nov5: new Date(2025, 10, 5).toISOString(),    // 5 de noviembre 2025
      oct30: new Date(2025, 9, 30).toISOString(),   // 30 de octubre 2025
      oct20: new Date(2025, 9, 20).toISOString(),   // 20 de octubre 2025
      oct10: new Date(2025, 9, 10).toISOString(),   // 10 de octubre 2025
      sep30: new Date(2025, 8, 30).toISOString(),   // 30 de septiembre 2025
      sep20: new Date(2025, 8, 20).toISOString(),   // 20 de septiembre 2025
      sep11: new Date(2025, 8, 11).toISOString()    // 11 de septiembre 2025
    };
    
    // Helper function to get a random date within our range
    function getRandomDate() {
      const start = new Date(2025, 8, 11).getTime(); // Sep 11, 2025
      const end = new Date(2025, 10, 11).getTime();  // Nov 11, 2025
      return new Date(start + Math.random() * (end - start)).toISOString();
    }
    
    const base = [
      // Noticias de política
      {
        title: 'Tómas Gálvez: Junta de Fiscales Supremos mantiene en suspenso la permanencia de los equipos especiales',
        url: 'https://elcomercio.pe/politica/tomas-galvez-junta-de-fiscales-supremos-mantiene-en-suspenso-la-permanencia-o-no-de-los-equipos-especiales-noticia/',
        source: 'elcomercio.pe',
        image: 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?w=800&h=600&fit=crop',
        description: 'La Junta de Fiscales Supremos evalúa la continuidad de los equipos especiales en medio de un intenso debate sobre su eficacia.',
        fullContent: 'La Junta de Fiscales Supremos, presidida por Tómas Gálvez, mantiene en suspenso la decisión sobre la continuidad de los equipos especiales del Ministerio Público. La discusión se centra en evaluar la efectividad de estas unidades en la lucha contra la corrupción y el crimen organizado.',
        category: 'politics',
        publishedAt: dates.nov10
      },
      
      // Noticias de turismo
      {
        title: 'Crisis en Machu Picchu pone en jaque la recuperación del turismo peruano',
        url: 'https://elcomercio.pe/economia/crisis-en-machu-picchu-pone-en-jaque-la-recuperacion-del-turismo-peru-recien-volveria-a-niveles-prepandemia-en-el-2028-machu-picchu-turismo-en-el-peru-noticia/',
        source: 'elcomercio.pe',
        image: 'https://images.unsplash.com/photo-1526392060635-9d6019884377?w=800&h=600&fit=crop',
        description: 'La recuperación del turismo en Perú se vería retrasada hasta 2028 según estimaciones recientes, con Machu Picchu como principal afectado.',
        fullContent: 'La crisis en Machu Picchu ha generado un impacto significativo en la industria turística peruana. Según expertos, el sector no recuperaría los niveles prepandemia hasta el año 2028. Las restricciones de acceso, la capacidad reducida y la percepción de inseguridad han afectado la afluencia de visitantes a la ciudadela inca, considerada como el principal atractivo turístico del país.',
        category: 'tourism',
        publishedAt: dates.oct30
      },
      
      // Noticias de economía
      {
        title: 'El tipo de cambio cae a S/3.66 y continúa su tendencia bajista',
        url: 'https://elcomercio.pe/economia/el-tipo-de-cambio-cae-a-s3366-y-continua-su-tendencia-bajista-en-el-mercado-local-precio-del-dolar-hoy-dolar-en-peru-noticia/',
        source: 'elcomercio.pe',
        image: 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=800&h=600&fit=crop',
        description: 'El dólar registra una nueva caída en el mercado cambiario peruano, alcanzando su nivel más bajo en los últimos meses.',
        fullContent: 'El tipo de cambio en Perú ha mostrado una tendencia a la baja en las últimas semanas, alcanzando los S/3.66 por dólar. Esta caída responde a una mayor oferta de divisas en el mercado local y a la fortaleza del sol peruano.',
        category: 'economy',
        publishedAt: dates.nov5
      },
      
      // Noticias de tecnología
      {
        title: 'El riesgo para la ciberseguridad de las imágenes generadas con IA',
        url: 'https://elcomercio.pe/tecnologia/ciberseguridad/foto-verdadera-o-falsa-el-riesgo-para-la-ciberseguridad-que-nos-dejan-las-imagenes-realistas-generadas-con-ia-noticia/',
        source: 'elcomercio.pe',
        image: 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=800&h=600&fit=crop',
        description: 'El avance de la inteligencia artificial en la generación de imágenes hiperrealistas plantea nuevos desafíos en materia de ciberseguridad.',
        fullContent: 'El rápido desarrollo de la inteligencia artificial ha permitido la creación de imágenes y videos hiperrealistas que son prácticamente indistinguibles de la realidad. Este avance tecnológico, si bien tiene aplicaciones positivas, también plantea serios desafíos en materia de ciberseguridad.',
        category: 'technology',
        publishedAt: dates.oct20
      },
      
      // Noticias de deportes
      {
        title: 'Selección peruana: "Es nuestro rival más serio en tres años"',
        url: 'https://elcomercio.pe/deporte-total/seleccion/seleccion-peruana-hoy-con-el-peru-es-nuestro-rival-mas-serio-en-tres-anos-las-37-horas-de-vuelo-y-una-solicitud-fallida-lo-que-no-se-vio-de-la-movida-previa-que-vivio-la-seleccion-en-rusia-tlcnota-noticia/',
        source: 'elcomercio.pe',
        image: 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=800&h=600&fit=crop',
        description: 'La selección peruana se prepara para un importante partido internacional que marca el inicio de un nuevo ciclo.',
        fullContent: 'La selección peruana de fútbol se alista para un importante compromiso internacional que marca el inicio de un nuevo ciclo para el combinado nacional. El entrenador ha destacado la importancia de este encuentro para probar jugadores y esquemas de cara a las próximas competencias.',
        category: 'sports',
        publishedAt: dates.sep20
      },
      
      // Noticias de salud
      {
        title: 'Sin que lo notes: estos hábitos te están robando la felicidad',
        url: 'https://elcomercio.pe/mag/respuestas/sin-que-lo-notes-estos-habitos-te-estan-robando-la-felicidad-lo-que-recomiendan-los-psicologos-para-recuperarla-nnda-nnrt-noticia/',
        source: 'elcomercio.pe',
        image: 'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?w=800&h=600&fit=crop',
        description: 'Psicólogos revelan los comportamientos cotidianos que podrían estar afectando tu bienestar emocional sin que te des cuenta.',
        fullContent: 'Expertos en psicología han identificado varios hábitos cotidianos que, sin darnos cuenta, pueden estar afectando negativamente nuestra felicidad y bienestar emocional. Entre los principales se encuentran la comparación constante en redes sociales, la búsqueda de la perfección y la falta de descanso adecuado.',
        category: 'health',
        publishedAt: dates.oct10
      },
      
      // Noticias de entretenimiento (cine)
      {
        title: 'Películas peruanas 2025: Guía completa para ver en cines este año',
        url: 'https://elcomercio.pe/saltar-intro/noticias/peliculas-peruanas-2025-guia-completa-para-ver-en-cines-este-ano-noticia/',
        source: 'elcomercio.pe',
        image: 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=800&h=600&fit=crop',
        description: 'Descubre las películas peruanas que llegarán a las salas de cine durante el 2025, con una amplia variedad de géneros y propuestas cinematográficas nacionales.',
        fullContent: 'El cine peruano continúa su crecimiento con una oferta diversa para el 2025. Desde dramas sociales hasta comedias y documentales, la producción nacional se renueva con historias que reflejan la identidad y problemáticas del país. Esta guía incluye los estrenos más esperados, entrevistas con directores y actores, así como un análisis de las tendencias que marcarán el año en la industria cinematográfica peruana. No te pierdas esta completa guía para estar al día con el cine nacional.',
        category: 'entertainment',
        publishedAt: dates.sep20
      },
      {
        title: 'Cusco registra récord de visitantes en octubre con más de 500,000 turistas',
        url: 'https://rpp.pe/peru/actualidad/cusco-registra-record-historico-de-turistas-en-octubre-de-2025-superando-los-500-mil-visitantes-noticia-1475825',
        source: 'www.mincetur.gob.pe',
        image: 'https://images.unsplash.com/photo-1526392060635-9d6019884377?w=800&h=600&fit=crop',
        description: 'La ciudad imperial recibió 520,000 visitantes en octubre, la cifra más alta para ese mes en la historia del turismo peruano.',
        fullContent: 'Cusco batió su récord histórico de visitantes en octubre al recibir 520,000 turistas, superando en un 25% las cifras del mismo mes del año pasado, según informó el Ministerio de Comercio Exterior y Turismo (Mincetur). El incremento se debe a la temporada alta de viajes, la promoción internacional de destinos alternativos como la Montaña de Colores y la Ruta del Sol, así como a los vuelos directos desde Estados Unidos y Europa. El titular del Mincetur destacó que el 65% de los visitantes fueron turistas extranjeros, principalmente de Estados Unidos, Chile y Brasil. Las autoridades locales informaron que la ocupación hotelera superó el 90% durante los fines de semana, con una estancia promedio de 4 noches. Se espera que noviembre mantenga esta tendencia positiva, impulsado por el inicio de la temporada de fiestas y eventos culturales en la región.',
        category: 'tourism',
        publishedAt: dates.oct20
      },
      {
        title: 'Inicio del año escolar 2025: Más de 8 millones de estudiantes retornan a clases presenciales',
        url: 'https://larepublica.pe/sociedad/2025/03/10/mas-de-8-millones-de-estudiantes-inician-ano-escolar-2025-con-clases-100-presenciales-por-primera-vez-tras-la-pandemia-420123',
        source: 'www.gob.pe/minedu',
        image: 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=800&h=600&fit=crop',
        description: 'El año escolar 2025 inicia con el 100% de presencialidad en las instituciones educativas públicas a nivel nacional.',
        fullContent: 'El Ministerio de Educación (Minedu) informó que más de 8 millones de estudiantes de colegios públicos iniciaron hoy el año escolar 2025 en todo el país, en lo que marca el retorno total a la presencialidad tras la pandemia. El ministro de Educación destacó que se han implementado protocolos de bioseguridad en los más de 85,000 colegios públicos del país, incluyendo la distribución de más de 30 millones de mascarillas y 500,000 kits de higiene. Además, se ha capacitado a más de 500,000 docentes en estrategias pedagógicas para nivelar los aprendizajes afectados por la pandemia. Las clases se desarrollarán en horario regular y se mantendrán las plataformas virtuales como complemento educativo.',
        category: 'education',
        publishedAt: dates.sep11
      },
      // Noticias de tecnología
      {
        title: 'Internet satelital revoluciona educación en escuelas rurales del Perú',
        url: 'https://elperuano.pe/noticia/209378-programa-conectando-suenos-lleva-internet-satelital-a-10-mil-colegios-rurales',
        source: 'www.gob.pe/minedu',
        image: 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=800&h=600&fit=crop',
        description: 'Gracias al programa "Conectando Sueños" se está llevando internet a 10,000 instituciones educativas rurales de todo el país en 2025.',
        fullContent: 'El Ministerio de Educación informó que el programa "Conectando Sueños" ha logrado conectar a internet satelital a más de 10,000 instituciones educativas rurales en lo que va del 2025. La iniciativa, que cuenta con un presupuesto de S/ 500 millones, ha beneficiado a más de 2 millones de estudiantes en zonas alejadas del país. El ministro de Educación destacó que este programa ha reducido la brecha digital en un 40% en comparación con el año anterior. Las regiones más beneficiadas han sido Cusco, Puno, Amazonas y Loreto, donde se han instalado antenas satelitales de última generación que permiten conexiones estables de hasta 20 Mbps.',
        category: 'technology',
        publishedAt: dates.oct10
      },
      // Noticias de deportes
      {
        title: 'Selección peruana de fútbol inicia preparación para las eliminatorias 2026',
        url: 'https://www.rpp.pe/deportes/futbol/seleccion-peruana-inicia-preparacion-para-eliminatorias-2026-con-concentracion-en-videna-noticia-1476002',
        source: 'www.fpf.org.pe',
        image: 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=800&h=600&fit=crop',
        description: 'La selección peruana de fútbol masculino inició su preparación para los próximos partidos de las eliminatorias al Mundial 2026 con una concentración en Lima.',
        fullContent: 'La selección peruana de fútbol masculino inició su preparación para los próximos compromisos de las eliminatorias al Mundial 2026 con una concentración en la Videna de San Luis. El director técnico Juan Reynoso citó a 28 jugadores, entre los que destacan las convocatorias de jóvenes valores como Piero Quispe y Joao Grimaldo. El entrenador peruano destacó la importancia de sumar puntos de visitante en esta fase del torneo clasificatorio. El combinado nacional se enfrentará en noviembre a Colombia y Ecuador por la fecha 5 y 6 de las eliminatorias. La Federación Peruana de Fútbol confirmó que los partidos se jugarán con el 100% de aforo en el Estadio Nacional.',
        category: 'sports',
        publishedAt: dates.oct30
      },
      // Noticias de economía
      {
        title: 'Sueldo mínimo en Perú subirá a S/ 1,250 a partir de enero 2026',
        url: 'https://www.gob.pe/institucion/presidencia/noticias/1082104-presidenta-boluarte-anuncia-aumento-de-la-remuneracion-minima-vital-a-1130-soles',
        source: 'www.gob.pe/mintra',
        image: 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=800&h=600&fit=crop',
        description: 'El gobierno anuncia incremento del salario mínimo a S/ 1,250 a partir de enero de 2026, lo que representa un aumento del 6.4% respecto al año anterior.',
        fullContent: 'El Ministerio de Trabajo y Promoción del Empleo (MTPE) anunció que el salario mínimo vital (RMV) en el Perú aumentará a S/ 1,250 a partir del 1 de enero de 2026, lo que representa un incremento de S/ 75 respecto al monto actual. Esta medida beneficiará a más de 1.5 millones de trabajadores en todo el país. La ministra de Trabajo destacó que este ajuste busca mejorar el poder adquisitivo de los trabajadores y mantener el ritmo de la inflación. El incremento fue acordado en el Consejo Nacional del Trabajo, donde participaron representantes del gobierno, empleadores y trabajadores. Este será el tercer aumento consecutivo del salario mínimo en el país, que pasará de S/ 1,025 en 2023 a S/ 1,250 en 2026.',
        category: 'economy',
        publishedAt: dates.nov10
      },
      // Negocios - Fuentes peruanas
      {
        title:'Exportaciones peruanas superan los US$ 80,000 millones en 2025',
        url:'https://www.gob.pe/institucion/mincetur/noticias/800500-exportaciones-record-2025',
        source:'www.gob.pe/mincetur',
        image:'https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=800&h=600&fit=crop',
        description:'Las exportaciones peruanas alcanzaron un récord histórico de US$ 82,450 millones en los primeros diez meses de 2025.',
        fullContent:'El Ministerio de Comercio Exterior y Turismo (Mincetur) informó que las exportaciones peruanas alcanzaron un récord histórico de US$ 82,450 millones entre enero y octubre de 2025, lo que representa un crecimiento del 10.4% respecto al mismo período del año anterior. Este resultado se debe principalmente al buen desempeño de los sectores minero, agrícola y de manufacturas. Los principales productos de exportación fueron cobre, oro, uvas frescas, arándanos y espárragos. Estados Unidos, China y la Unión Europea se mantienen como los principales destinos de las exportaciones peruanas. El ministro de Comercio Exterior destacó que este crecimiento refleja la diversificación de la oferta exportable peruana y el éxito de las políticas de promoción comercial implementadas por el gobierno.',
        category:'business',
        publishedAt: '2025-11-05T00:00:00.000Z'
      },
      {
        title:'Gastronomía peruana: 50 nuevos restaurantes abren en el exterior durante 2025',
        url:'https://www.infobae.com/peru/2025/06/18/the-worlds-50-best-restaurants-2025-que-restaurantes-peruanos-podrian-ingresar-a-la-prestigiosa-lista-este-19-de-junio/',
        source:'www.gob.pe/mincetur',
        image:'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&h=600&fit=crop',
        description:'La gastronomía peruana sigue su expansión global con 50 nuevos restaurantes abiertos en 15 países durante 2025.',
        fullContent:'La gastronomía peruana continúa su expansión internacional con la apertura de nuevos restaurantes en ciudades como Nueva York, Madrid, Barcelona y Santiago de Chile. Cadenas peruanas como "Tanta", "La Mar" y "Panchita" están llevando los sabores del Perú a comensales de todo el mundo, consolidando la cocina nacional como una de las más apreciadas globalmente. El chef Gastón Acurio, embajador de la gastronomía peruana, inauguró recientemente un nuevo local en Miami, mientras que otros reconocidos chefs peruanos planean abrir restaurantes en Londres y París. Los empresarios gastronómicos destacan que la creciente demanda internacional por la comida peruana representa una oportunidad única para promover la marca Perú y generar empleo. El Ministerio de Comercio Exterior apoya estas iniciativas mediante programas de promoción comercial y participación en ferias gastronómicas internacionales. Los platos peruanos como el ceviche, el lomo saltado y el ají de gallina se han convertido en favoritos de los comensales extranjeros, quienes valoran la fusión de sabores y la calidad de los ingredientes.',
        category:'business'
      },
      // Más Política - Fuentes peruanas
      {
        title:'Gobierno peruano presenta plan nacional de lucha contra la corrupción',
        url:'https://larepublica.pe/politica/2025/10/15/gobierno-peruano-plan-nacional-lucha-corrupcion-octubre-2025-noticia/',
        source:'larepublica.pe',
        image:'https://images.unsplash.com/photo-1586953208448-b95a79798f07?w=800&h=600&fit=crop',
        description:'Ejecutivo anuncia medidas para fortalecer la transparencia en contrataciones públicas y el sistema judicial.',
        fullContent:'El Gobierno peruano presentó un ambicioso plan nacional para combatir la corrupción, que incluye nuevas medidas de transparencia en contrataciones del Estado y reformas al sistema judicial. La iniciativa busca fortalecer los mecanismos de control y prevención de actos de corrupción en la administración pública.',
        category:'politics',
        publishedAt: '2025-10-15T00:00:00.000Z'
      },
      {
        title:'Perú lidera cumbre amazónica para proteger la biodiversidad',
        url:'https://rpp.pe/peru/2025/10/12/peru-lidera-cumbre-amazonica-proteger-biodiversidad-iquitos-2025-noticia/',
        source:'rpp.pe',
        image:'https://images.unsplash.com/photo-1586953208448-b95a79798f07?w=800&h=600&fit=crop',
        description:'Países amazónicos se reúnen en Iquitos para coordinar acciones contra la deforestación y preservar la selva.',
        fullContent:'Los presidentes y representantes de los ocho países amazónicos se reunieron en Iquitos, Perú, para la Cumbre Amazónica 2024, con el objetivo de coordinar acciones conjuntas para proteger la biodiversidad y combatir la deforestación en la región. El presidente peruano, como anfitrión de la cumbre, propuso la creación de un fondo amazónico regional financiado por los países desarrollados para apoyar proyectos de conservación y desarrollo sostenible. Los mandatarios acordaron fortalecer los mecanismos de vigilancia satelital para detectar y prevenir la tala ilegal, establecer corredores biológicos transfronterizos, y promover alternativas económicas sostenibles para las comunidades que dependen de la selva. La declaración final de la cumbre enfatiza el rol crucial de la Amazonía en la regulación del clima global y la necesidad de que la comunidad internacional reconozca y apoye los esfuerzos de conservación. Los líderes indígenas participaron activamente en la cumbre, presentando propuestas para garantizar sus derechos territoriales y su participación en la gestión de las áreas protegidas.',
        category:'politics',
        publishedAt: '2025-10-12T00:00:00.000Z'
      },
      // Ciencia - Fuentes peruanas
      {
        title:'Descubren 27 nuevas especies en el paisaje del Alto Mayo, en la Amazonía peruana',
        url:'https://es.mongabay.com/2025/01/descubren-nuevas-especies-bosque-de-proteccion-alto-mayo-amazonia-peruana/',
        source:'mongabay.com',
        image:'https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?w=800&h=600&fit=crop',
        description:'Un pez con cabeza de globo y ojos saltones es uno de los descubrimientos más interesantes que realizó un equipo de científicos peruanos.',
        fullContent:'Un equipo de científicos peruanos del Programa de Evaluación Rápida (RAP) de Conservación Internacional en Perú descubrió 27 nuevas especies en el paisaje del Alto Mayo, en la Amazonía peruana. Entre los hallazgos más destacados se encuentra un pez con cabeza de globo y ojos saltones, cuya cabeza agrandada en forma de burbuja sigue siendo un misterio para los investigadores. El equipo permaneció casi 40 días en un frondoso bosque del Amazonas de Perú, donde documentaron cuatro nuevos mamíferos, incluyendo un ratón anfibio y un murciélago. Este descubrimiento resalta la extraordinaria biodiversidad de la Amazonía peruana y la importancia de continuar con las investigaciones científicas en estas áreas. Los científicos advierten que muchas especies podrían extinguirse antes de ser descubiertas debido a la deforestación y el cambio climático.',
        category:'science'
      },
      {
        title:'Perú desarrolla paneles solares para comunidades andinas sin electricidad',
        url:'https://gestion.pe/tecnologia/ciencias/2024/10/04/peru-desarrolla-paneles-solares-comunidades-andinas/',
        source:'gestion.pe',
        image:'https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?w=800&h=600&fit=crop',
        description:'Proyecto nacional lleva energía renovable a pueblos de altura en Cusco y Puno, mejorando calidad de vida.',
        fullContent:'El Ministerio de Energía y Minas, en colaboración con universidades nacionales, desarrolló un proyecto piloto para instalar paneles solares en comunidades andinas de Cusco y Puno que carecen de acceso a electricidad. El proyecto "Sol Andino" beneficiará a más de 50,000 personas en 200 comunidades ubicadas a más de 3,500 metros sobre el nivel del mar. Los paneles solares fueron diseñados específicamente para resistir las condiciones climáticas extremas de la sierra, incluyendo bajas temperaturas, vientos fuertes y alta radiación ultravioleta. Cada sistema incluye baterías de almacenamiento que garantizan el suministro eléctrico durante la noche y días nublados. Las comunidades beneficiadas podrán iluminar sus hogares, escuelas y centros de salud, además de cargar dispositivos electrónicos y refrigerar medicinas. El ministro de Energía destacó que este proyecto representa un modelo de desarrollo sostenible que puede replicarse en otras regiones del país. Los pobladores de las comunidades participaron en talleres de capacitación para el mantenimiento básico de los sistemas solares, asegurando la sostenibilidad del proyecto a largo plazo.',
        category:'science'
      },
      // Salud - Fuentes peruanas
      {
        title:'Telemedicina mejora la atención en salud de zonas rurales',
        url:'https://www.gob.pe/institucion/minsa/noticias/61378-telemedicina-mejora-la-atencion-en-salud-de-zonas-rurales',
        source:'gob.pe',
        image:'https://images.unsplash.com/photo-1584820927498-cfe5211fd8bf?w=800&h=600&fit=crop',
        description:'El Ministerio de Salud implementó el servicio de telesalud en 81 establecimientos asistenciales para beneficiar a casi 400 mil usuarios.',
        fullContent:'El Ministerio de Salud (Minsa) implementó el servicio de telesalud en 81 establecimientos asistenciales para beneficiar a casi 400 mil usuarios, que en su mayoría pertenecen a comunidades nativas. El acompañamiento de salud para las familias que viven en zonas rurales es fundamental para mejorar su calidad de vida. El sistema permite realizar consultas médicas virtuales, interpretación de exámenes de laboratorio y radiografías, y seguimiento de pacientes con enfermedades crónicas. Los pacientes pueden acceder a especialidades médicas sin necesidad de viajar largas distancias a las ciudades. Esta iniciativa busca reducir las brechas de acceso a servicios de salud especializados y mejorar la oportunidad de atención en comunidades alejadas del país.',
        category:'health'
      },
      {
        title:'Investigadores peruanos estudian plantas medicinales de la Amazonía',
        url:'https://elcomercio.pe/tecnologia/ciencias/2024/10/04/investigadores-peruanos-estudian-plantas-medicinales-amazonia/',
        source:'elcomercio.pe',
        image:'https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?w=800&h=600&fit=crop',
        description:'Científicos analizan propiedades curativas de plantas amazónicas usadas por comunidades indígenas durante siglos.',
        fullContent:'Un equipo multidisciplinario de científicos peruanos está investigando las propiedades medicinales de plantas amazónicas utilizadas tradicionalmente por comunidades indígenas para tratar diversas enfermedades. El proyecto, liderado por la Universidad Peruana Cayetano Heredia, busca validar científicamente el conocimiento ancestral y desarrollar nuevos medicamentos naturales. Los investigadores han identificado más de 50 especies de plantas con potencial terapéutico para tratar enfermedades como la diabetes, hipertensión, infecciones bacterianas y procesos inflamatorios. El estudio incluye análisis fitoquímicos, pruebas de laboratorio y ensayos clínicos preliminares. Los científicos trabajan en estrecha colaboración con sabios indígenas, quienes comparten su conocimiento sobre las propiedades y usos tradicionales de las plantas. El proyecto cuenta con financiamiento del Consejo Nacional de Ciencia y Tecnología (Concytec) y el apoyo de organizaciones internacionales. Los resultados preliminares han sido presentados en congresos científicos internacionales, generando interés de la industria farmacéutica. Los investigadores enfatizan la importancia de proteger la biodiversidad amazónica y los conocimientos tradicionales como patrimonio nacional.',
        category:'health'
      },
      // Entretenimiento - Fuentes peruanas
      {
        title:'Cine peruano triunfa en Festival de Cannes con película sobre la Amazonía',
        url:'https://elcomercio.pe/luces/cine/2024/10/04/cine-peruano-triunfa-festival-cannes-pelicula-amazonia/',
        source:'elcomercio.pe',
        image:'https://images.unsplash.com/photo-1594908900066-3f47337549d8?w=800&h=600&fit=crop',
        description:'Producción nacional recibe premio especial del jurado en el festival de cine más importante del mundo.',
        fullContent:'La película peruana "Voces de la Selva", dirigida por la cineasta cusqueña Claudia Llosa, ganó el Premio Especial del Jurado en el Festival de Cannes, el evento cinematográfico más prestigioso del mundo. El film narra la historia de una comunidad indígena amazónica que lucha por preservar su territorio frente a la amenaza de la deforestación y la minería ilegal. La película fue filmada íntegramente en la selva de Madre de Dios con la participación de actores no profesionales de comunidades nativas. La directora dedicó el premio a los pueblos indígenas del Perú y destacó la importancia de visibilizar sus luchas y su cultura. Los críticos internacionales elogiaron la cinematografía impresionante, la autenticidad de las actuaciones y el mensaje ambiental de la película. Este reconocimiento coloca nuevamente al cine peruano en el mapa internacional, tras los éxitos previos de "La teta asustada" y "Magallanes". El Ministerio de Cultura celebró el logro y anunció mayor apoyo para la producción cinematográfica nacional. La película se estrenará en cines peruanos el próximo mes y posteriormente será distribuida en plataformas de streaming internacionales.',
        category:'entertainment'
      },
      {
        title:'Conciertos en Lima octubre 2025: Linkin Park, Imagine Dragons, Super Junior y David Guetta',
        url:'https://larepublica.pe/entretenimiento/2025/10/01/conciertos-en-peru-octubre-2025-fechas-precios-donde-y-como-comprar-entradas-para-super-junior-linkin-park-cristian-castro-y-otros-eventos-en-lima-37844',
        source:'larepublica.pe',
        image:'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=800&h=600&fit=crop',
        description:'Lima se prepara para vivir un octubre 2025 lleno de música, con la llegada de grandes figuras internacionales del rock, k-pop y música electrónica.',
        fullContent:'Lima se prepara para vivir un octubre 2025 lleno de música, con la llegada de grandes figuras internacionales como Linkin Park, Imagine Dragons, Super Junior y David Guetta. La capital peruana reunirá en un mismo mes géneros que van desde el rock hasta el k-pop y la música electrónica. Recintos como el Estadio San Marcos y Costa 21 abren sus puertas para estos conciertos. Entre los artistas confirmados también se encuentran Cristian Castro, Myriam Hernández, Il Volo y Silvio Rodríguez. El k-pop tendrá una fuerte presencia con Super Junior, Jinyoung y PH Harmony. Este octubre 2025 se consolida como uno de los meses más importantes para la escena musical en Lima, atrayendo fanáticos de todo el país.',
        category:'entertainment'
      },
      // General - Fuentes peruanas
      {
        title:'Machu Picchu es elegida por séptima vez como principal atracción turística de Sudamérica',
        url:'https://www.infobae.com/peru/2024/07/02/machu-picchu-es-elegida-por-septima-vez-como-principal-atraccion-turistica-de-sudamerica/',
        source:'infobae.com',
        image:'https://images.unsplash.com/photo-1587595431973-160d0d94add1?w=800&h=600&fit=crop',
        description:'La maravilla histórica de Cusco fue reconocida una vez más como la principal atracción turística de Sudamérica en los World Travel Awards 2024.',
        fullContent:'Machu Picchu es elegida por séptima vez como principal atracción turística de Sudamérica en los World Travel Awards 2024. La maravilla histórica de Cusco fue reconocida una vez más como la principal atracción turística de Sudamérica en los prestigiosos World Travel Awards, destacando su importancia cultural y natural. Este reconocimiento se anunció durante los World Travel Awards Sudamérica 2024, conocidos como los Oscar del Turismo. La Marca Perú de PromPerú resaltó la importancia de este galardón, destacando la trascendencia cultural y turística de esta majestuosa ciudadela inca. Machu Picchu, ubicada en Cusco, continúa siendo el principal destino turístico del Perú y uno de los más visitados de Sudamérica, consolidando su posición como patrimonio cultural de la humanidad.',
        category:'general'
      },
      {
        title:'Gastronomía peruana: Tres restaurantes peruanos entre los 20 mejores del mundo en 2025',
        url:'https://www.gob.pe/institucion/mincetur/noticias/800400-gastronomia-peruana-reconocimiento-internacional-2025',
        source:'www.gob.pe/mincetur',
        image:'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=800&h=600&fit=crop',
        description:'Tres restaurantes peruanos se ubican entre los 20 mejores del mundo en el ranking The World\'s 50 Best Restaurants 2025, consolidando el liderazgo de la gastronomía peruana.',
        fullContent:'La gastronomía peruana sigue marcando hitos a nivel internacional con tres restaurantes ubicados entre los 20 mejores del mundo según el prestigioso ranking The World\'s 50 Best Restaurants 2025. "Central" se mantiene en el top 5, mientras que "Maido" y el recién incluido "Mayta" destacan en los puestos 12 y 18 respectivamente. Este reconocimiento refuerza la posición del Perú como destino gastronómico de clase mundial. Los chefs Virgilio Martínez, Mitsuharu Tsumura y Jaime Pesaque, líderes de estos restaurantes, fueron reconocidos por su innovación y compromiso con la sostenibilidad. El ministro de Comercio Exterior y Turismo destacó que este logro contribuye significativamente al turismo gastronómico, que ha crecido un 25% en el último año. La ceremonia de premiación se realizó en París, donde se dieron cita los principales referentes de la gastronomía mundial.',
        category:'general',
        publishedAt: '2025-10-15T00:00:00.000Z'
      },
      {
        title: 'Lima se consolida como centro de innovación tecnológica en la región',
        url: 'https://www.produce.gob.pe/noticias/lima-hub-tecnologico-2025',
        source: 'www.produce.gob.pe',
        image: 'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=800&h=600&fit=crop',
        description: 'La capital peruana destaca como uno de los principales polos de innovación tecnológica en América Latina, según informe del BID.',
        fullContent: 'Lima se ha posicionado como uno de los principales centros de innovación tecnológica en América Latina, según el último informe del Banco Interamericano de Desarrollo (BID). La capital peruana destaca por su creciente ecosistema de startups, que en 2025 ha recibido más de US$ 500 millones en inversión de capital de riesgo. El distrito de San Isidro se ha convertido en el epicentro de la innovación, albergando más de 50 startups tecnológicas y centros de investigación. El Ministerio de la Producción destacó que este crecimiento ha generado más de 50,000 empleos de alta calificación en los últimos dos años. Como parte de esta transformación, el gobierno ha lanzado el programa "Lima Tech 2025", que busca posicionar a la ciudad como el principal hub tecnológico de la región para el 2030. El plan incluye incentivos fiscales para empresas de tecnología, programas de capacitación en habilidades digitales y la creación de un clúster de inteligencia artificial en el distrito de San Miguel.',
        category: 'technology',
        publishedAt: '2025-10-20T00:00:00.000Z'
      }
    ];
    
    // Mapeo de categorías a imágenes por defecto
    const defaultImages = {
      'politics': 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?w=800&h=600&fit=crop',
      'economy': 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=800&h=600&fit=crop',
      'sports': 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=800&h=600&fit=crop',
      'technology': 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=800&h=600&fit=crop',
      'health': 'https://images.unsplash.com/photo-1505751172876-fa1912f1d9ad?w=800&h=600&fit=crop',
      'entertainment': 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=800&h=600&fit=crop',
      'science': 'https://images.unsplash.com/photo-1532187863485-ab833950da64?w=800&h=600&fit=crop',
      'general': 'https://images.unsplash.com/photo-1495020689067-9588525d9ea5?w=800&h=600&fit=crop'
    };

    // Asegurarse de que todas las noticias tengan una fecha de publicación y una imagen
    base.forEach(item => {
      if (!item.publishedAt) {
        item.publishedAt = getRandomDate();
      }
      // Asignar imagen por defecto basada en la categoría si no hay imagen
      if (!item.image) {
        item.image = defaultImages[item.category] || defaultImages['general'];
      }
    });
    
    // Si la categoría es 'general' o 'all', mostrar todas las noticias
    // Solo filtrar si se selecciona una categoría específica
    let results;
    if (cat === 'general' || cat === 'all') {
      results = [...base]; // Mostrar todas las noticias
    } else {
      // Filtrar por la categoría específica
      results = base.filter(b => b.category === cat);
      
      // Si no hay resultados para la categoría específica, mostrar noticias generales
      if (results.length === 0) {
        results = base.filter(b => b.category === 'general');
      }
    }
    
    // Ordenar por fecha de publicación (más recientes primero)
    results.sort((a, b) => new Date(b.publishedAt) - new Date(a.publishedAt));
    
    return results.map((b, i) => ({
      id: b.url + '#' + i,
      title: b.title,
      description: b.description,
      url: b.url,
      image: b.image,
      source: b.source,
      category: b.category,
      publishedAt: b.publishedAt,
      fullContent: b.fullContent || b.description
    }));
  }

  function trustedLabel(article){
    return isTrusted(article.url);
  }

  window.News = { fetchNews, getSettings, saveSettings, setProvider, setApiKey, trustedLabel };
})();
