#include <GLUT/glut.h>
#include <SDL2/SDL.h>
#include <SDL2/SDL_mixer.h>
#include <cmath>
#include <iostream>
#include <vector>
#include <ctime>
#include <cstdlib>

#ifndef M_PI
#define M_PI 3.14159265358979323846
#endif



using namespace std;

// ---------------------------------------------
//  CONFIGURACION GLOBAL
// ---------------------------------------------
static const int   WINDOW_W      = 1280;
static const int   WINDOW_H      = 720;
static const float PARK_HALF     = 55.0f;
static const float TIMER_MS      = 16;

// ---------------------------------------------
//  CAMARA
// ---------------------------------------------
static float camX   =  0.0f;
static float camY   = 18.0f;
static float camZ   = 70.0f;
static float camYaw =  0.0f;
static float camPit = -8.0f;

// ---------------------------------------------
//  MOUSE
// ---------------------------------------------
static bool  mouseLeftDown  = false;
static bool  mouseRightDown = false;
static int   mouseLastX     = 0;
static int   mouseLastY     = 0;

// Control de carro
static int    selectedCar = -1;  // -1 = ninguno seleccionado
static bool   carControlled = false;  // Si hay un carro controlado
static int    lastClickTime = 0;  // Para detectar doble clic

static const float MOUSE_YAW_SPEED  = 0.35f;
static const float MOUSE_PIT_SPEED  = 0.25f;
static const float MOUSE_PAN_SPEED  = 0.06f;
static const float MOUSE_ZOOM_STEP  = 2.5f;

// ---------------------------------------------
//  ANIMACION
// ---------------------------------------------
static bool  animEnabled      = true;

// ---------------------------------------------
//  CONTROL DE ILUMINACION
// ---------------------------------------------
enum LightingMode { DAY, AFTERNOON, NIGHT };
static LightingMode currentLighting = DAY;

// Declaraciones de funciones de iluminación
void setupDayLighting();
void setupAfternoonLighting();
void setupNightLighting();

// Declaraciones de funciones de audio
void initAudio();
void loadAudioFiles();
void playMusic(int musicId);
void stopMusic();

// Rueda de la fortuna
static float wheelAngle       = 0.0f;
static const float WHEEL_SPEED = 0.4f;

// Torre de caida
static float towerPlatformY   = 0.0f;
static int   towerPhase       = 0;
static float towerTimer       = 0.0f;
static const float TOWER_HEIGHT_TRAVEL = 12.0f;
static const float TOWER_RISE_SPEED    =  0.08f;
static const float TOWER_FALL_SPEED    =  0.35f;
static const float TOWER_PAUSE_TIME    = 60.0f;

// Controles manuales de la torre de caída
static bool  towerManualMode  = false;
static float towerManualSpeed = 0.1f;

// Sillas voladoras PRO
static float chairsAngle  = 0.0f;
static float currentSwing = 0.0f;
static float targetSwing  = 28.0f;
static float rotationVel  = 0.0f;
static float chairHeight   = 0.0f;
static int   speedCycle    = 0; // 0: acelerando, 1: frenando, 2: pausa
static float cycleTimer    = 0.0f;

// Montaña rusa
static float coasterT = 0.0f;

// Gusanito
static float wormAngle = 0.0f;

//  CONTROL DE ATRACCIONES (MENÚ)
static bool animRuletaRusa = true;
static bool animSillasVoladoras = true;
static bool animCarritosChocones = true;
static bool animMontanaRusa = true;
static bool animGusanito = true;
static bool animTorreCaida = true;
static bool animCirco = true;

//  CONTROL DE SONIDOS (MENÚ)
static bool sonidoActivado = false;
static bool sonidoAmbiente = false;
static bool musicaParque = false;
static int musicaActual = 0;

// Variables para SDL_mixer
static Mix_Music* musicAmbiente = NULL;
static Mix_Music* musicBoleteria = NULL;
static Mix_Music* musicRuleta = NULL;
static Mix_Music* musicSillas = NULL;
static Mix_Music* musicCarritos = NULL;
static Mix_Music* musicMontana = NULL;
static Mix_Music* musicGusanito = NULL;
static Mix_Music* musicTorre = NULL;
static Mix_Music* musicCirco = NULL;

//  ENUMERACIONES DEL MENÚ

enum OpcionesMenu {
    // Tiempo del día (0-2)
    MENU_DIA = 0,
    MENU_TARDE = 1,
    MENU_NOCHE = 2,

    // Sonidos (10-20)
    MENU_SONIDO_ACTIVAR_TODO = 10,
    MENU_SONIDO_DESACTIVAR_TODO = 11,
    MENU_SONIDO_AMBIENTE = 12,
    MENU_MUSICA_PARQUE = 13,
    MENU_SONIDO_BOLETERIA = 14,
    MENU_SONIDO_RULETA = 15,
    MENU_SONIDO_SILLAS = 16,
    MENU_SONIDO_CARRITOS = 17,
    MENU_SONIDO_MONTANA = 18,
    MENU_SONIDO_GUSANITO = 19,
    MENU_SONIDO_TORRE = 20,
    MENU_SONIDO_CIRCO = 21,

    // Atracciones (30-36)
    MENU_ATTR_RULETA_RUSA = 30,
    MENU_ATTR_SILLAS_VOLADORAS = 31,
    MENU_ATTR_CARRITOS_CHOCONES = 32,
    MENU_ATTR_MONTANA_RUSA = 33,
    MENU_ATTR_GUSANITO = 34,
    MENU_ATTR_TORRE_CAIDA = 35,
    MENU_ATTR_CIRCO = 36
};

// ---------------------------------------------
//  CARROS CHOCONES - VARIABLES GLOBALES
// ---------------------------------------------
const int BUMPER_CARS_COUNT = 4;
const float BUMPER_ARENA_RADIUS = 14.0f;

struct Color { float r,g,b; };

struct Particle {
    float x,y,z;
    float vx,vy,vz;
    float life;
    Color c;
};

struct BumperCar {
    float x,z;
    float vx,vz;
    float angle;
    Color c;
};

std::vector<BumperCar> bumperCars;
std::vector<Particle> bumperParticles;

// ---------------------------------------------
//  CARROS CHOCONES - FUNCIONES AUXILIARES
// ---------------------------------------------
static void mat(float r,float g,float b,bool em=false){
    // Forzar color con glColor para asegurar que se vea diferente
    glColor3f(r, g, b);
    
    GLfloat d[] = {r, g, b, 1.0f};
    GLfloat s[] = {0.35f, 0.35f, 0.35f, 1.0f};
    GLfloat e[] = {r*0.15f, g*0.15f, b*0.15f, 1.0f};
    GLfloat z[] = {0,0,0,1};
    
    glMaterialfv(GL_FRONT_AND_BACK, GL_DIFFUSE, d);
    glMaterialfv(GL_FRONT_AND_BACK, GL_SPECULAR, s);
    glMaterialf(GL_FRONT_AND_BACK, GL_SHININESS, 25.0f);
    glMaterialfv(GL_FRONT_AND_BACK, GL_EMISSION, em ? e : z);
    
    // Asegurar que el material se aplique inmediatamente
    glEnable(GL_COLOR_MATERIAL);
    glColorMaterial(GL_FRONT_AND_BACK, GL_AMBIENT_AND_DIFFUSE);
}

static void spawnParticle(float x,float z,Color c){
    for(int i=0;i<20;i++){
        Particle p;
        p.x=x; p.y=0.5f; p.z=z;
        p.vx=(rand()%100-50)/120.0f;
        p.vy=(rand()%100)/100.0f;
        p.vz=(rand()%100-50)/120.0f;
        p.life=1;
        p.c=c;
        bumperParticles.push_back(p);
    }
}

static void updateParticles(){
    for(int i=0;i<(int)bumperParticles.size();){
        Particle &p=bumperParticles[i];
        p.x+=p.vx;
        p.y+=p.vy;
        p.z+=p.vz;
        p.vy-=0.01f;
        p.life-=0.02f;
        if(p.life<=0){
            bumperParticles[i]=bumperParticles.back();
            bumperParticles.pop_back();
        } else i++;
    }
}

// ---------------------------------------------
//  CUADRATICO COMPARTIDO
// ---------------------------------------------
static GLUquadric* gQuad = 0;

// ---------------------------------------------
//  UTILIDADES DE COLOR
// ---------------------------------------------
static inline void setColor(float r, float g, float b)
{
    glColor3f(r, g, b);
}

// ---------------------------------------------
//  PRIMITIVAS DE APOYO
// ---------------------------------------------

static void drawCylinderY(float radius, float height, int slices, int stacks) {
    glPushMatrix();
    glRotatef(-90, 1, 0, 0);
    gluCylinder(gQuad, radius, radius, height, slices, stacks);
    glPopMatrix();
}

static void drawBox(float sx, float sy, float sz) {
    glPushMatrix();
    glScalef(sx, sy, sz);
    glutSolidCube(1.0f);
    glPopMatrix();
}

static void drawLantern() {
    setColor(0.25f, 0.25f, 0.25f);
    drawCylinderY(0.12f, 4.5f, 8, 1);
    glPushMatrix();
    glTranslatef(0, 4.7f, 0);
    setColor(1.0f, 0.95f, 0.7f);
    glutSolidSphere(0.35f, 12, 12);
    glPopMatrix();
}

static void drawBench() {
    setColor(0.55f, 0.35f, 0.15f);
    glPushMatrix(); glTranslatef(0, 0.6f, 0); drawBox(2.0f, 0.15f, 0.7f); glPopMatrix();
    glPushMatrix(); glTranslatef(0, 1.1f, -0.28f); drawBox(2.0f, 0.9f, 0.1f); glPopMatrix();
    setColor(0.4f, 0.25f, 0.1f);
    float pataX[2] = {-0.8f, 0.8f};
    for (int i = 0; i < 2; ++i) {
        glPushMatrix(); glTranslatef(pataX[i], 0.3f, 0); drawBox(0.12f, 0.6f, 0.65f); glPopMatrix();
    }
}
static void drawBenchAt(float x, float z, float rotY) {
    glPushMatrix();
    glTranslatef(x, 0, z);
    glRotatef(rotY, 0, 1, 0);
    drawBench();
    glPopMatrix();
}
static void drawTree() {
    setColor(0.45f, 0.28f, 0.10f);
    drawCylinderY(0.25f, 2.5f, 8, 1);
    setColor(0.15f, 0.55f, 0.15f);
    glPushMatrix(); glTranslatef(0, 3.5f, 0); glutSolidSphere(1.6f, 14, 14); glPopMatrix();
}

static void drawStreetLight() {
    // Poste metálico
    setColor(0.3f, 0.3f, 0.35f);
    drawCylinderY(0.12f, 5.0f, 10, 1);
    
    // Brazo extendido
    glPushMatrix();
    glTranslatef(0, 4.8f, 0);
    glRotatef(-90, 1, 0, 0);
    glRotatef(45, 0, 1, 0);
    gluCylinder(gQuad, 0.08f, 0.06f, 1.5f, 8, 1);
    glPopMatrix();
    
    // Lámpara - al final del brazo (1.5f de longitud en ángulo 45°)
    glPushMatrix();
    // Calcular posición final del brazo: 1.5 * cos(45°) ? 1.06
    glTranslatef(1.06f, 4.8f, 1.06f);
    setColor(0.95f, 0.95f, 0.7f);
    glutSolidSphere(0.25f, 12, 12);
    glPopMatrix();
    
    // Base
    glPushMatrix();
    glTranslatef(0, 0.15f, 0);
    setColor(0.4f, 0.4f, 0.45f);
    drawBox(0.5f, 0.3f, 0.5f);
    glPopMatrix();
}
void setupLights() {
    glEnable(GL_LIGHTING);
    glEnable(GL_LIGHT0);
    glEnable(GL_LIGHT1);
    glEnable(GL_COLOR_MATERIAL);
    glColorMaterial(GL_FRONT_AND_BACK, GL_AMBIENT_AND_DIFFUSE);

    GLfloat lightPos0[] = {40.0f, 50.0f, 40.0f, 1.0f};
    GLfloat lightAmb0[] = {0.35f, 0.35f, 0.35f, 1.0f};
    GLfloat lightDif0[] = {1.0f, 1.0f, 1.0f, 1.0f};
    GLfloat lightSpec0[] = {1.0f, 1.0f, 1.0f, 1.0f};

    glLightfv(GL_LIGHT0, GL_POSITION, lightPos0);
    glLightfv(GL_LIGHT0, GL_AMBIENT, lightAmb0);
    glLightfv(GL_LIGHT0, GL_DIFFUSE, lightDif0);
    glLightfv(GL_LIGHT0, GL_SPECULAR, lightSpec0);

    GLfloat lightPos1[] = {0.0f, 15.0f, 0.0f, 1.0f};
    GLfloat lightDif1[] = {1.0f, 0.2f, 0.2f, 1.0f};

    glLightfv(GL_LIGHT1, GL_POSITION, lightPos1);
    glLightfv(GL_LIGHT1, GL_DIFFUSE, lightDif1);

    GLfloat mat_specular[] = {1.0, 1.0, 1.0, 1.0};
    GLfloat mat_shininess[] = {50.0};

    glMaterialfv(GL_FRONT, GL_SPECULAR, mat_specular);
    glMaterialfv(GL_FRONT, GL_SHININESS, mat_shininess);
}

void setupDayLighting() {
    glEnable(GL_LIGHTING);
    glEnable(GL_LIGHT0);
    glEnable(GL_LIGHT1);
    glEnable(GL_COLOR_MATERIAL);
    glColorMaterial(GL_FRONT_AND_BACK, GL_AMBIENT_AND_DIFFUSE);

    // Luz principal del sol (blanca brillante)
    GLfloat lightPos0[] = {40.0f, 80.0f, 40.0f, 1.0f};
    GLfloat lightAmb0[] = {0.45f, 0.45f, 0.45f, 1.0f};
    GLfloat lightDif0[] = {1.0f, 1.0f, 0.95f, 1.0f};
    GLfloat lightSpec0[] = {1.0f, 1.0f, 1.0f, 1.0f};

    glLightfv(GL_LIGHT0, GL_POSITION, lightPos0);
    glLightfv(GL_LIGHT0, GL_AMBIENT, lightAmb0);
    glLightfv(GL_LIGHT0, GL_DIFFUSE, lightDif0);
    glLightfv(GL_LIGHT0, GL_SPECULAR, lightSpec0);

    // Luz de relleno suave
    GLfloat lightPos1[] = {-30.0f, 40.0f, -30.0f, 1.0f};
    GLfloat lightDif1[] = {0.3f, 0.3f, 0.35f, 1.0f};

    glLightfv(GL_LIGHT1, GL_POSITION, lightPos1);
    glLightfv(GL_LIGHT1, GL_DIFFUSE, lightDif1);

    // Luz ambiental global
    GLfloat globalAmb[] = {0.5f, 0.5f, 0.5f, 1.0f};
    glLightModelfv(GL_LIGHT_MODEL_AMBIENT, globalAmb);

    // Material
    GLfloat mat_specular[] = {1.0, 1.0, 1.0, 1.0};
    GLfloat mat_shininess[] = {50.0};
    glMaterialfv(GL_FRONT, GL_SPECULAR, mat_specular);
    glMaterialfv(GL_FRONT, GL_SHININESS, mat_shininess);
}

void setupAfternoonLighting() {
    glEnable(GL_LIGHTING);
    glEnable(GL_LIGHT0);
    glEnable(GL_LIGHT1);
    glEnable(GL_COLOR_MATERIAL);
    glColorMaterial(GL_FRONT_AND_BACK, GL_AMBIENT_AND_DIFFUSE);

    // Luz del sol atardecer (naranja/roja)
    GLfloat lightPos0[] = {60.0f, 25.0f, 40.0f, 1.0f};
    GLfloat lightAmb0[] = {0.35f, 0.25f, 0.2f, 1.0f};
    GLfloat lightDif0[] = {1.0f, 0.6f, 0.3f, 1.0f};
    GLfloat lightSpec0[] = {0.9f, 0.7f, 0.5f, 1.0f};

    glLightfv(GL_LIGHT0, GL_POSITION, lightPos0);
    glLightfv(GL_LIGHT0, GL_AMBIENT, lightAmb0);
    glLightfv(GL_LIGHT0, GL_DIFFUSE, lightDif0);
    glLightfv(GL_LIGHT0, GL_SPECULAR, lightSpec0);

    // Luz de relleno azulada (cielo)
    GLfloat lightPos1[] = {-40.0f, 30.0f, -20.0f, 1.0f};
    GLfloat lightDif1[] = {0.4f, 0.5f, 0.7f, 1.0f};

    glLightfv(GL_LIGHT1, GL_POSITION, lightPos1);
    glLightfv(GL_LIGHT1, GL_DIFFUSE, lightDif1);

    // Luz ambiental global más cálida
    GLfloat globalAmb[] = {0.35f, 0.3f, 0.25f, 1.0f};
    glLightModelfv(GL_LIGHT_MODEL_AMBIENT, globalAmb);

    // Material
    GLfloat mat_specular[] = {0.9, 0.8, 0.7, 1.0};
    GLfloat mat_shininess[] = {45.0};
    glMaterialfv(GL_FRONT, GL_SPECULAR, mat_specular);
    glMaterialfv(GL_FRONT, GL_SHININESS, mat_shininess);
}

void setupNightLighting() {
    glEnable(GL_LIGHTING);
    glEnable(GL_LIGHT0);
    glEnable(GL_LIGHT1);
    glEnable(GL_COLOR_MATERIAL);
    glColorMaterial(GL_FRONT_AND_BACK, GL_AMBIENT_AND_DIFFUSE);

    // Luz principal tenue (luna)
    GLfloat lightPos0[] = {30.0f, 60.0f, 30.0f, 1.0f};
    GLfloat lightAmb0[] = {0.1f, 0.1f, 0.15f, 1.0f};
    GLfloat lightDif0[] = {0.4f, 0.4f, 0.6f, 1.0f};
    GLfloat lightSpec0[] = {0.3f, 0.3f, 0.5f, 1.0f};

    glLightfv(GL_LIGHT0, GL_POSITION, lightPos0);
    glLightfv(GL_LIGHT0, GL_AMBIENT, lightAmb0);
    glLightfv(GL_LIGHT0, GL_DIFFUSE, lightDif0);
    glLightfv(GL_LIGHT0, GL_SPECULAR, lightSpec0);

    // Luz de relleno cálida (luces de la ciudad)
    GLfloat lightPos1[] = {0.0f, 10.0f, 0.0f, 1.0f};
    GLfloat lightDif1[] = {0.8f, 0.5f, 0.2f, 1.0f};

    glLightfv(GL_LIGHT1, GL_POSITION, lightPos1);
    glLightfv(GL_LIGHT1, GL_DIFFUSE, lightDif1);

    // Luz ambiental global muy tenue
    GLfloat globalAmb[] = {0.15f, 0.15f, 0.2f, 1.0f};
    glLightModelfv(GL_LIGHT_MODEL_AMBIENT, globalAmb);

    // Material
    GLfloat mat_specular[] = {0.5, 0.5, 0.6, 1.0};
    GLfloat mat_shininess[] = {30.0};
    glMaterialfv(GL_FRONT, GL_SPECULAR, mat_specular);
    glMaterialfv(GL_FRONT, GL_SHININESS, mat_shininess);
}

//  FUNCIONES DE AUDIO (SDL_mixer)
void initAudio() {
    if (SDL_Init(SDL_INIT_AUDIO) < 0) {
        printf("SDL_Init falló: %s\n", SDL_GetError());
        return;
    }

    if (Mix_OpenAudio(44100, MIX_DEFAULT_FORMAT, 2, 2048) < 0) {
        printf("Mix_OpenAudio falló: %s\n", Mix_GetError());
        return;
    }

    printf("SDL_mixer inicializado correctamente\n");
}

void loadAudioFiles() {
    musicAmbiente = Mix_LoadMUS("musica/musica_ambiente.MP3");
    if (musicAmbiente) printf("✓ musica_ambiente.MP3 cargado\n");
    else printf("✗ Error cargando musica_ambiente.MP3: %s\n", Mix_GetError());

    musicBoleteria = Mix_LoadMUS("musica/boleteria.mp3");
    if (musicBoleteria) printf("✓ boleteria.mp3 cargado\n");
    else printf("✗ Error cargando boleteria.mp3: %s\n", Mix_GetError());

    musicRuleta = Mix_LoadMUS("musica/ruleta_rusa.mp3");
    if (musicRuleta) printf("✓ ruleta_rusa.mp3 cargado\n");
    else printf("✗ Error cargando ruleta_rusa.mp3: %s\n", Mix_GetError());

    musicSillas = Mix_LoadMUS("musica/silla_voladora.mp3");
    if (musicSillas) printf("✓ silla_voladora.mp3 cargado\n");
    else printf("✗ Error cargando silla_voladora.mp3: %s\n", Mix_GetError());

    musicCarritos = Mix_LoadMUS("musica/carros_chocones.mp3");
    if (musicCarritos) printf("✓ carros_chocones.mp3 cargado\n");
    else printf("✗ Error cargando carros_chocones.mp3: %s\n", Mix_GetError());

    musicMontana = Mix_LoadMUS("musica/montana_rusa.mp3");
    if (musicMontana) printf("✓ montana_rusa.mp3 cargado\n");
    else printf("✗ Error cargando montana_rusa.mp3: %s\n", Mix_GetError());

    musicGusanito = Mix_LoadMUS("musica/gusanito.mp3");
    if (musicGusanito) printf("✓ gusanito.mp3 cargado\n");
    else printf("✗ Error cargando gusanito.mp3: %s\n", Mix_GetError());

    musicTorre = Mix_LoadMUS("musica/torre_caida.mp3");
    if (musicTorre) printf("✓ torre_caida.mp3 cargado\n");
    else printf("✗ Error cargando torre_caida.mp3: %s\n", Mix_GetError());

    musicCirco = Mix_LoadMUS("musica/musica_circo.mp3");
    if (musicCirco) printf("✓ musica_circo.mp3 cargado\n");
    else printf("✗ Error cargando musica_circo.mp3: %s\n", Mix_GetError());

    printf("Archivos de audio cargados\n");
}

void playMusic(int musicId) {
    stopMusic();

    Mix_Music* music = NULL;

    switch (musicId) {
    case 0: music = musicAmbiente; break;
    case 8: music = musicBoleteria; break;
    case 1: music = musicRuleta; break;
    case 2: music = musicSillas; break;
    case 3: music = musicCarritos; break;
    case 4: music = musicMontana; break;
    case 5: music = musicGusanito; break;
    case 6: music = musicTorre; break;
    case 7: music = musicCirco; break;
    }

    if (music) {
        if (Mix_PlayMusic(music, -1) == -1) {
            printf("Error reproduciendo música: %s\n", Mix_GetError());
        } else {
            printf("Música reproduciéndose\n");
        }
    }
}

void stopMusic() {
    if (Mix_PlayingMusic()) {
        Mix_HaltMusic();
        printf("Música detenida\n");
    }
}

//luces en los postes 
void drawLampPost(float x, float z) {

    // Poste
    setColor(0.2f, 0.2f, 0.2f);

    glPushMatrix();
    glTranslatef(x, 4.0f, z);
    drawCylinderY(0.15f, 8.0f, 12, 3);
    glPopMatrix();

    // Lámpara
    setColor(1.0f, 1.0f, 0.3f);

    glPushMatrix();
    glTranslatef(x, 8.5f, z);
    glutSolidSphere(0.4f, 20, 20);
    glPopMatrix();

    // Luz real
    GLfloat pos[] = {x, 8.5f, z, 1.0f};
    GLfloat dif[] = {1.0f, 1.0f, 0.7f, 1.0f};

    glLightfv(GL_LIGHT2, GL_POSITION, pos);
    glLightfv(GL_LIGHT2, GL_DIFFUSE, dif);

    glEnable(GL_LIGHT2);
}

// ---------------------------------------------
//  TERRENO Y CAMINOS
// ---------------------------------------------
static void drawTerrain() {
    glDisable(GL_LIGHTING);
    setColor(0.22f, 0.48f, 0.18f);
    glBegin(GL_QUADS);
    glNormal3f(0, 1, 0);
    glVertex3f(-PARK_HALF, 0, -PARK_HALF);
    glVertex3f( PARK_HALF, 0, -PARK_HALF);
    glVertex3f( PARK_HALF, 0,  PARK_HALF);
    glVertex3f(-PARK_HALF, 0,  PARK_HALF);
    glEnd();

    setColor(0.62f, 0.60f, 0.55f);
    glBegin(GL_QUADS);
    glVertex3f(-PARK_HALF, 0.01f, -3.5f);
    glVertex3f( PARK_HALF, 0.01f, -3.5f);
    glVertex3f( PARK_HALF, 0.01f,  3.5f);
    glVertex3f(-PARK_HALF, 0.01f,  3.5f);
    
    glVertex3f(-28.0f, 0.01f, -PARK_HALF);
    glVertex3f(-21.0f, 0.01f, -PARK_HALF);
    glVertex3f(-21.0f, 0.01f,  3.5f);
    glVertex3f(-28.0f, 0.01f,  3.5f);
    
    glVertex3f( 21.0f, 0.01f, -3.5f);
    glVertex3f( 28.0f, 0.01f, -3.5f);
    glVertex3f( 28.0f, 0.01f,  PARK_HALF);
    glVertex3f( 21.0f, 0.01f,  PARK_HALF);
    
    glVertex3f(-PARK_HALF, 0.01f, -28.0f);
    glVertex3f(-35.0f, 0.01f, -28.0f);
    glVertex3f(-35.0f, 0.01f, -21.0f);
    glVertex3f(-PARK_HALF, 0.01f, -21.0f);
    
    glVertex3f( 35.0f, 0.01f, -28.0f);
    glVertex3f( PARK_HALF, 0.01f, -28.0f);
    glVertex3f( PARK_HALF, 0.01f, -21.0f);
    glVertex3f( 35.0f, 0.01f, -21.0f);
    
    glVertex3f(-PARK_HALF, 0.01f, 21.0f);
    glVertex3f(-35.0f, 0.01f, 21.0f);
    glVertex3f(-35.0f, 0.01f, 28.0f);
    glVertex3f(-PARK_HALF, 0.01f, 28.0f);
    glEnd();
    glEnable(GL_LIGHTING);
    //luces del poste 
    drawLampPost(-20, -20);
    drawLampPost(20, -20);
    drawLampPost(-20, 20);
    drawLampPost(20, 20);
}

static void drawFence() {
    setColor(0.60f, 0.40f, 0.20f);
    float step = 5.0f;
    
    // Entrada ubicación para abertura en la reja - EN EL BORDE FRONTAL, CAMINO DERECHO
    const float entranceX = 24.5f; // Centro del camino derecho (21-28)
    const float entranceZ = 55.0f; // Borde frontal del parque
    const float openingWidth = 16.0f; // Ancho de la abertura
    
    for (float t = -PARK_HALF; t <= PARK_HALF; t += step) {
        // Lado trasero (Z = -PARK_HALF) - sin abertura
        glPushMatrix(); glTranslatef(t, 1.8f, -PARK_HALF); drawBox(0.25f, 3.6f, 0.25f); glPopMatrix();
        
        // Lado frontal (Z = PARK_HALF) - con abertura en la entrada (X = 0)
        if (fabs(t - entranceX) > openingWidth / 2) {
            glPushMatrix(); glTranslatef(t, 1.8f, PARK_HALF); drawBox(0.25f, 3.6f, 0.25f); glPopMatrix();
        }
        
        // Lados izquierdo y derecho - sin abertura (completos)
        glPushMatrix(); glTranslatef(-PARK_HALF, 1.8f, t); drawBox(0.25f, 3.6f, 0.25f); glPopMatrix();
        glPushMatrix(); glTranslatef( PARK_HALF, 1.8f, t); drawBox(0.25f, 3.6f, 0.25f); glPopMatrix();
    }
    
    glDisable(GL_LIGHTING);
    setColor(0.50f, 0.32f, 0.12f);
    glLineWidth(2.5f);
    glBegin(GL_LINES);
    float yLevels[3] = {1.0f, 2.4f, 3.6f};
    for (int i = 0; i < 3; ++i) {
        float y = yLevels[i];
        // Línea trasera (completa)
        glVertex3f(-PARK_HALF, y, -PARK_HALF); glVertex3f(PARK_HALF, y, -PARK_HALF);
        
        // Línea frontal con abertura en X = 0 (entrada principal)
        glVertex3f(-PARK_HALF, y, PARK_HALF); glVertex3f(entranceX - openingWidth/2, y, PARK_HALF);
        glVertex3f(entranceX + openingWidth/2, y, PARK_HALF); glVertex3f(PARK_HALF, y, PARK_HALF);
        
        // Líneas laterales (completas)
        glVertex3f(-PARK_HALF, y, -PARK_HALF); glVertex3f(-PARK_HALF, y, PARK_HALF);
        glVertex3f(PARK_HALF, y, -PARK_HALF); glVertex3f(PARK_HALF, y, PARK_HALF);
    }
    glEnd();
    glEnable(GL_LIGHTING);
}

static void drawEntrance() {
    // Entrada alineada al camino derecho en el BORDE del parque
    const float entranceX = 24.5f; // Centro del camino derecho (21-28)
    const float entranceZ = 55.0f; // Borde frontal del parque
    
    // Pilares de entrada más altos y prominentes
    setColor(0.85f, 0.82f, 0.75f);
    float pilarX[2] = {-7.0f, 7.0f};
    for (int i = 0; i < 2; ++i) {
        glPushMatrix(); 
        glTranslatef(entranceX + pilarX[i], 5.0f, entranceZ); 
        drawBox(2.0f, 10.0f, 2.0f); 
        glPopMatrix();
        
        // Bases decorativas
        setColor(0.6f, 0.6f, 0.6f);
        glPushMatrix();
        glTranslatef(entranceX + pilarX[i], 0.3f, entranceZ);
        drawBox(3.0f, 0.6f, 3.0f);
        glPopMatrix();
        setColor(0.85f, 0.82f, 0.75f);
    }
    
    // Arco horizontal superior
    glPushMatrix(); 
    glTranslatef(entranceX, 10.0f, entranceZ); 
    drawBox(16.0f, 1.5f, 1.5f); 
    glPopMatrix();
    
    // Cartel de entrada - Fondo rojo (sin letras)
    setColor(0.95f, 0.15f, 0.15f);
    glPushMatrix(); 
    glTranslatef(entranceX, 11.5f, entranceZ + 0.8f); 
    drawBox(14.0f, 2.5f, 0.2f); 
    glPopMatrix();
    
    // Borde dorado del cartel
    setColor(1.0f, 0.85f, 0.2f);
    glPushMatrix(); 
    glTranslatef(entranceX, 11.5f, entranceZ + 0.7f); 
    drawBox(14.5f, 3.0f, 0.1f); 
    glPopMatrix();
}
//======================================================
// CIRCO 
//======================================================
static void drawSmallCircus(float x, float z)
{
    glPushMatrix();
    glTranslatef(x,0,z);
    const int slices=50;
    //---------------------------
    // BASE DEL CIRCO
    //----------------------------
    setColor(0.55f,0.35f,0.15f);

    glBegin(GL_TRIANGLE_FAN);
    glVertex3f(0,0,0);

    for(int i=0;i<=slices;i++)
    {
        float a=2*M_PI*i/slices;
        glVertex3f(12*cos(a),0,12*sin(a));
    }
    glEnd();
    //----------------------------
    // PAREDES CON RAYAS
    //----------------------------
    for(int i=0;i<slices;i++)
    {
        float a1=2*M_PI*i/slices;
        float a2=2*M_PI*(i+1)/slices;
        if(i%2==0)
            setColor(0.95f,0.1f,0.1f);
        else
            setColor(1,1,1);
        glBegin(GL_QUADS);
        glVertex3f(10*cos(a1),0,10*sin(a1));
        glVertex3f(10*cos(a2),0,10*sin(a2));
        glVertex3f(10*cos(a2),7,10*sin(a2));
        glVertex3f(10*cos(a1),7,10*sin(a1));
        glEnd();
    }
    //---------------------------
    // TECHO CARPA REAL
    //----------------------------
    for(int i=0;i<slices;i++)
    {
        float a1=2*M_PI*i/slices;
        float a2=2*M_PI*(i+1)/slices;
        if(i%2==0)
            setColor(1,1,1);
        else
            setColor(0.85f,0,0);
        glBegin(GL_TRIANGLES);
        glVertex3f(0,17,0);
        glVertex3f(
        10*cos(a1),
        7,
        10*sin(a1));
        glVertex3f(
        10*cos(a2),
        7,
        10*sin(a2));
        glEnd();
    }
    //----------------------------
    // MASTIL CENTRAL
    //----------------------------
    setColor(0.4f,0.25f,0.1f);
    glBegin(GL_QUADS);
    glVertex3f(-0.3,0,-0.3);
    glVertex3f(0.3,0,-0.3);
    glVertex3f(0.3,19,-0.3);
    glVertex3f(-0.3,19,-0.3);
    glEnd();
    //--------------------------
    // BANDERA ARRIBA
    //----------------------------
    setColor(1,1,0);
    glBegin(GL_TRIANGLES);
    glVertex3f(0,19,0);
    glVertex3f(3,18,0);
    glVertex3f(0,17,0);
    glEnd();
    //----------------------------
    // ENTRADA DEL CIRCO
    //----------------------------
    setColor(0.75f,0,0);
    glBegin(GL_QUADS);
    glVertex3f(-2,0,10.05);
    glVertex3f(2,0,10.05);
    glVertex3f(2,5,10.05);
    glVertex3f(-2,5,10.05);
    glEnd();
    //----------------------------
    // TECHO ENTRADA
    //----------------------------
    setColor(1,1,1);
    glBegin(GL_TRIANGLES);
    glVertex3f(0,8,10.2);
    glVertex3f(-3,5,10.2);
    glVertex3f(3,5,10.2);
    glEnd();
    //----------------------------
    // MINI BANDERITAS EXTERIORES
    //----------------------------
    for(int i=0;i<12;i++)
    {
        float a=2*M_PI*i/12;
        float bx=12*cos(a);
        float bz=12*sin(a);
        setColor(1,1,0);
        glBegin(GL_TRIANGLES);
        glVertex3f(bx,9,bz);
        glVertex3f(
        bx+1*cos(a),
        8,
        bz+1*sin(a));
        glVertex3f(
        bx-1*cos(a),
        8,
        bz-1*sin(a));
        glEnd();
    }
    glPopMatrix();
}
// ---------------------------------------------
//  KIOSCO DE DULCES 
// ---------------------------------------------
static void drawSweetKiosk(float x, float z, float rotacion) {
    glPushMatrix();
    glTranslatef(x, 0.0f, z);
    glRotatef(rotacion, 0, 1, 0); 
    
    // Base 
    setColor(0.4f, 0.4f, 0.4f);
    glPushMatrix();
    glTranslatef(0.0f, 0.25f, 0.0f);
    drawBox(4.2f, 0.5f, 4.2f);
    glPopMatrix();
    
    // Cuerpo (Rosa caramelo)
    setColor(0.95f, 0.6f, 0.8f);
    glPushMatrix();
    glTranslatef(0.0f, 2.0f, 0.0f);
    drawBox(3.8f, 3.0f, 3.8f);
    glPopMatrix();
    
    // Ventanas
    setColor(0.85f, 0.95f, 1.0f);
    float posVentanitas[] = {-1.2f, 0.0f, 1.2f};
    for(int i = 0; i < 3; i++) {
        glPushMatrix();
        glTranslatef(posVentanitas[i], 2.2f, 1.91f);
        drawBox(0.8f, 1.0f, 0.05f);
        glPopMatrix();
    }
    
  
    setColor(0.2f, 0.2f, 0.2f); // Techo gris oscuro elegante
    glPushMatrix();
    glTranslatef(0.0f, 3.7f, 0.0f);
    drawBox(5.0f, 0.3f, 5.0f); // Techo plano y amplio
    glPopMatrix();
    
    // Borde decorativo del techo 
    setColor(0.8f, 0.1f, 0.1f);
    glPushMatrix();
    glTranslatef(0.0f, 3.5f, 0.0f);
    drawBox(5.2f, 0.3f, 5.2f);
    glPopMatrix();
    
    glPopMatrix();
}
// ---------------------------------------------
//  BOLETER A (VERSI N M S GRANDE)
// ---------------------------------------------
static void drawTicketBooth() {
    // Ajustamos la posici n (X=11.5) para que quepa bien por su nuevo tama o
    glPushMatrix();
    glTranslatef(11.5f, 0.0f, 52.5f);
    
    // Base/Plataforma de la boleter a (M s grande)
    setColor(0.4f, 0.4f, 0.4f);
    glPushMatrix();
    glTranslatef(0.0f, 0.25f, 0.0f);
    drawBox(6.5f, 0.5f, 6.5f);
    glPopMatrix();
    
    // Paredes principales (Estructura azul vistosa escalada)
    setColor(0.15f, 0.4f, 0.75f);
    glPushMatrix();
    glTranslatef(0.0f, 3.25f, 0.0f);
    drawBox(5.5f, 5.5f, 5.5f);
    glPopMatrix();
    
    // Ventanilla de atenci n ampliada (Gris claro en la cara delantera)
    setColor(0.85f, 0.85f, 0.85f);
    glPushMatrix();
    glTranslatef(0.0f, 3.2f, 2.76f);
    drawBox(3.0f, 2.2f, 0.05f);
    glPopMatrix();
    
    // Repisa de la ventanilla (Madera extendida)
    setColor(0.55f, 0.35f, 0.15f);
    glPushMatrix();
    glTranslatef(0.0f, 2.1f, 2.9f);
    drawBox(3.4f, 0.2f, 0.5f);
    glPopMatrix();
    
    // Techo inclinado decorativo (Rojo y m s amplio)
    setColor(0.9f, 0.2f, 0.2f);
    glPushMatrix();
    glTranslatef(0.0f, 6.25f, 0.0f);
    glScalef(6.2f, 0.8f, 6.2f);
    glutSolidCube(1.0f);
    glPopMatrix();
    
    // Cartel superior "TICKETS" (Fondo Amarillo gigante)
    setColor(0.95f, 0.85f, 0.2f);
    glPushMatrix();
    glTranslatef(0.0f, 7.2f, 2.85f);
    drawBox(4.0f, 1.2f, 0.1f);
    glPopMatrix();
    
    glPopMatrix();
}
// ---------------------------------------------
//  ZONA A: RUEDA DE LA FORTUNA
// ---------------------------------------------

// Función auxiliar para dibujar cabina centrada en (0,0,0)
static void drawCabin(int cabinIndex) {
    // Barra de cuelgue
    setColor(0.30f, 0.30f, 0.30f);
    glPushMatrix();
    glRotatef(90, 1, 0, 0);
    gluCylinder(gQuad, 0.12f, 0.12f, 2.8f, 8, 1);
    glPopMatrix();
    
    glTranslatef(0, -2.8f, 0);
    
    // Cabina con colores variados
    float cabina_r = 0.20f + (cabinIndex * 0.08f);
    float cabina_g = 0.40f + (cabinIndex * 0.06f);
    float cabina_b = 0.88f - (cabinIndex * 0.04f);
    setColor(cabina_r, cabina_g, cabina_b);
    
    // Base de cabina - centrada en (0,0,0)
    drawBox(1.8f, 0.5f, 1.8f);
    
    // Techo de cabina
    glPushMatrix();
    glTranslatef(0, 0.70f, -0.72f);
    drawBox(1.8f, 1.40f, 0.14f);
    glPopMatrix();
    
    // Barras laterales
    setColor(0.18f, 0.35f, 0.75f);
    float lateralX[2] = {-0.84f, 0.84f};
    for (int j = 0; j < 2; ++j) {
        glPushMatrix();
        glTranslatef(lateralX[j], 0.55f, -0.22f);
        drawBox(0.12f, 1.1f, 1.36f);
        glPopMatrix();
    }
    
    // Barras horizontales
    setColor(0.25f, 0.25f, 0.30f);
    glPushMatrix();
    glTranslatef(0, 0.3f, 0);
    drawBox(1.6f, 0.08f, 1.6f);
    glPopMatrix();
}

static void drawFerrisWheel() {
    const float CX = -40.0f, CZ = -30.0f; // Más a la izquierda
    const float RADIUS = 10.5f;  // Radio consistente para TODO
    const float WHEEL_CENTER_Y = 16.0f; // Altura del centro de la rueda
    
    glPushMatrix();
    glTranslatef(CX, 0, CZ);

    // Materiales metálicos para estructura
    GLfloat metal_ambient[] = {0.25f, 0.25f, 0.25f, 1.0f};
    GLfloat metal_diffuse[] = {0.7f, 0.7f, 0.75f, 1.0f};
    GLfloat metal_specular[] = {0.9f, 0.9f, 0.95f, 1.0f};
    GLfloat metal_shininess[] = {80.0f};
    
    glMaterialfv(GL_FRONT, GL_AMBIENT, metal_ambient);
    glMaterialfv(GL_FRONT, GL_DIFFUSE, metal_diffuse);
    glMaterialfv(GL_FRONT, GL_SPECULAR, metal_specular);
    glMaterialfv(GL_FRONT, GL_SHININESS, metal_shininess);

    // Base de hormigon
    setColor(0.65f, 0.65f, 0.65f);
    glPushMatrix();
    glTranslatef(0, 0.4f, 0);
    drawBox(6.0f, 0.8f, 4.0f);
    glPopMatrix();

    // Soportes principales alineados con el aro
    setColor(0.55f, 0.55f, 0.60f);
    // Soporte izquierdo
    glPushMatrix();
    glTranslatef(-RADIUS + 1.0f, 0, 0);
    glRotatef(12, 0, 0, 1);
    glTranslatef(0, 9.5f, 0);
    drawBox(1.2f, 19.0f, 1.2f);
    glPopMatrix();

    // Soporte derecho
    glPushMatrix();
    glTranslatef(RADIUS - 1.0f, 0, 0);
    glRotatef(-12, 0, 0, 1);
    glTranslatef(0, 9.5f, 0);
    drawBox(1.2f, 19.0f, 1.2f);
    glPopMatrix();

    // Travesano
    glPushMatrix();
    glTranslatef(0, 3.5f, 0);
    drawBox(RADIUS * 2, 0.9f, 0.9f);
    glPopMatrix();

    // Barras diagonales
    setColor(0.45f, 0.45f, 0.50f);
    for (int i = 0; i < 4; i++) {
        float angle = i * 90.0f;
        glPushMatrix();
        glRotatef(angle, 0, 1, 0);
        glTranslatef(RADIUS - 2.5f, 8.0f, 0);
        glRotatef(45, 1, 0, 0);
        drawBox(0.3f, 6.0f, 0.3f);
        glPopMatrix();
    }

    // Eje central alineado perfectamente
    setColor(0.85f, 0.85f, 0.15f);
    glPushMatrix();
    glTranslatef(0, WHEEL_CENTER_Y, 0);
    glRotatef(90, 1, 0, 0);
    gluCylinder(gQuad, 0.8f, 0.8f, 2.5f, 20, 1);
    gluCylinder(gQuad, 0.6f, 0.6f, 2.8f, 20, 1);
    glPopMatrix();

    // Rueda giratoria - todo gira alrededor del mismo centro
    glPushMatrix();
    glTranslatef(0, WHEEL_CENTER_Y, 0);
    glRotatef(wheelAngle, 0, 0, 1);

    // Aro exterior - usa el radio exacto WHEEL_RADIUS
    GLfloat red_ambient[] = {0.3f, 0.1f, 0.1f, 1.0f};
    GLfloat red_diffuse[] = {0.85f, 0.18f, 0.18f, 1.0f};
    GLfloat red_specular[] = {0.6f, 0.2f, 0.2f, 1.0f};
    glMaterialfv(GL_FRONT, GL_AMBIENT, red_ambient);
    glMaterialfv(GL_FRONT, GL_DIFFUSE, red_diffuse);
    glMaterialfv(GL_FRONT, GL_SPECULAR, red_specular);
    
    setColor(0.85f, 0.18f, 0.18f);
    glutSolidTorus(0.55f, RADIUS, 22, 36);

    // Aro interior decorativo
    GLfloat gold_ambient[] = {0.2f, 0.15f, 0.05f, 1.0f};
    GLfloat gold_diffuse[] = {0.90f, 0.55f, 0.10f, 1.0f};
    GLfloat gold_specular[] = {0.8f, 0.6f, 0.2f, 1.0f};
    glMaterialfv(GL_FRONT, GL_AMBIENT, gold_ambient);
    glMaterialfv(GL_FRONT, GL_DIFFUSE, gold_diffuse);
    glMaterialfv(GL_FRONT, GL_SPECULAR, gold_specular);
    
    setColor(0.90f, 0.55f, 0.10f);
    glutSolidTorus(0.30f, RADIUS - 3.5f, 16, 28);

    // Aro de refuerzo
    setColor(0.75f, 0.75f, 0.80f);
    glutSolidTorus(0.15f, RADIUS - 2.0f, 16, 32);

    // Centro / hub mejorado y centrado
    GLfloat hub_ambient[] = {0.2f, 0.2f, 0.05f, 1.0f};
    GLfloat hub_diffuse[] = {0.95f, 0.88f, 0.10f, 1.0f};
    GLfloat hub_specular[] = {0.9f, 0.8f, 0.3f, 1.0f};
    glMaterialfv(GL_FRONT, GL_AMBIENT, hub_ambient);
    glMaterialfv(GL_FRONT, GL_DIFFUSE, hub_diffuse);
    glMaterialfv(GL_FRONT, GL_SPECULAR, hub_specular);
    
    setColor(0.95f, 0.88f, 0.10f);
    glutSolidSphere(1.2f, 20, 20);

    // Luces decorativas animadas - DENTRO del bloque de rotación
    const int LIGHTS = 20;
    for (int i = 0; i < LIGHTS; i++) {
        float angle = i * (360.0f / LIGHTS);

        glPushMatrix();
        
        // 1. Posicionarte en el aro (igual que cabinas y radios)
        glRotatef(angle, 0, 0, 1);
        glTranslatef(RADIUS, 0, 0);

        // 2. Parpadeo animado
        float t = glutGet(GLUT_ELAPSED_TIME) * 0.005f;
        float intensity = 0.5f + 0.5f * sin(t + i);
        glColor3f(intensity, 0.2f, 0.2f);

        // 3. Dibujar luz
        glutSolidSphere(0.2f, 10, 10);

        glPopMatrix();
    }

    // Radios con sistema correcto de coordenadas
    const int SPOKES = 8;
    for (int i = 0; i < SPOKES; ++i) {
        float spokeAngle = i * (360.0f / SPOKES);
        
        // Radio: sistema correcto
        glPushMatrix();
        glRotatef(spokeAngle, 0, 0, 1); 
        glRotatef(90, 0, 1, 0);           // Alinear con Y
        setColor(0.68f, 0.68f, 0.72f); 
        gluCylinder(gQuad, 0.15f, 0.15f, RADIUS, 10, 1); // Longitud exacta
        glPopMatrix();

        // Cabina con ORDEN CORRECTO de transformaciones
        glPushMatrix();
        
        // 1. Rotar para posicionarte en el ángulo correcto
        glRotatef(spokeAngle, 0, 0, 1);
        
        // 2. Ir EXACTAMENTE al borde del aro
        glTranslatef(RADIUS, 0, 0);
        
        // 3. Cancelar rotación de la rueda ? mantener cabina vertical
        glRotatef(-wheelAngle, 0, 0, 1);
        
        // 4. Dibujar cabina centrada
        drawCabin(i);
        
        glPopMatrix();
    }
    glPopMatrix();
    glPopMatrix();
    
    // Sombra simple debajo de la rueda
    glDisable(GL_LIGHTING);
    glColor4f(0.0f, 0.0f, 0.0f, 0.4f);
    glPushMatrix();
    glTranslatef(CX, 0.01f, CZ);
    glScalef(1.0f, 0.01f, 1.0f);
    glRotatef(wheelAngle * 0.3f, 0, 1, 0); // Rotación suave de la sombra
    glutSolidTorus(0.55f, RADIUS, 22, 36);
    glPopMatrix();
    glEnable(GL_LIGHTING);
    
    // Bancas Zona A - colocadas al borde de la vereda y mirando hacia el camino
    drawBenchAt(-20.0f, -14.0f, -90.0f);
    drawBenchAt(-29.0f, -14.0f,  90.0f);
    
    // Árboles Zona A - MÁS ÁRBOLES (fuera del juego)
    float treeA_X[8] = {-33.0f, -17.0f, -48.0f, -8.0f, -45.0f, -12.0f, -50.0f, -10.0f};
    float treeA_Z[8] = {-42.0f, -42.0f, -30.0f, -30.0f, -15.0f, -15.0f, -22.0f, -22.0f};
    for (int k = 0; k < 8; ++k) {
        glPushMatrix();
        glTranslatef(treeA_X[k], 0, treeA_Z[k]);
        drawTree();
        glPopMatrix();
    }

}

// ---------------------------------------------
//  ZONA B: TORRE DE CAIDA
// ---------------------------------------------
static void drawDropTower() {
    const float CX = 35.0f, CZ = 25.0f;
    glPushMatrix();
    glTranslatef(CX, 0, CZ);

    setColor(0.60f, 0.60f, 0.60f);
    glPushMatrix(); glTranslatef(0, 0.5f, 0); drawBox(4.0f, 1.0f, 4.0f); glPopMatrix();

    const float MAST_H = 28.0f;
    const float MAST_R = 0.7f;
    setColor(0.55f, 0.55f, 0.60f);
    drawCylinderY(MAST_R, MAST_H, 16, 1);

    setColor(0.68f, 0.68f, 0.72f);
    float guideOff = 2.0f;
    float guideX[4] = {-guideOff, -guideOff,  guideOff,  guideOff};
    float guideZ[4] = {-guideOff,  guideOff, -guideOff,  guideOff};
    for (int g = 0; g < 4; ++g) {
        glPushMatrix(); glTranslatef(guideX[g], 0, guideZ[g]); drawCylinderY(0.15f, MAST_H, 8, 1); glPopMatrix();
    }

    setColor(0.85f, 0.18f, 0.18f);
    glPushMatrix(); glTranslatef(0, MAST_H + 1.5f, 0); glutSolidSphere(1.2f, 16, 16); glPopMatrix();
    glPushMatrix(); glTranslatef(0, MAST_H, 0); drawCylinderY(1.8f, 1.0f, 16, 1); glPopMatrix();

    setColor(0.95f, 0.18f, 0.18f);
    float franjas[4] = {6.0f, 12.0f, 18.0f, 24.0f};
    for (int fi = 0; fi < 4; ++fi) {
        glPushMatrix(); glTranslatef(0, franjas[fi], 0); drawCylinderY(MAST_R + 0.08f, 0.5f, 16, 1); glPopMatrix();
    }

// ========================================
// CABINA DE LA TORRE DE CAÍDA
// ========================================
glPushMatrix();
glTranslatef(0, 2.5f + towerPlatformY, 0);

// aro principal
setColor(0.95f, 0.75f, 0.05f);
glutSolidTorus(0.25f, 3.0f, 16, 32);

const int SEATS = 8;

for (int s = 0; s < SEATS; s++)
{
    glPushMatrix();

    float angle = s * (360.0f / SEATS);

    glRotatef(angle, 0, 1, 0);

    // distancia desde el centro
    glTranslatef(3.3f, 0, 0);

    // =====================
    // SOPORTE AL ARO
    // =====================
    setColor(0.40f, 0.40f, 0.40f);

    glPushMatrix();
    glTranslatef(-0.15f, 0.25f, 0);
    drawBox(0.08f, 0.7f, 0.08f);
    glPopMatrix();

    glPushMatrix();
    glTranslatef(0.15f, 0.25f, 0);
    drawBox(0.08f, 0.7f, 0.08f);
    glPopMatrix();

    // barra superior
    glPushMatrix();
    glTranslatef(0, 0.55f, 0);
    drawBox(0.35f, 0.08f, 0.08f);
    glPopMatrix();

    // =====================
    // ASIENTO
    // =====================
    setColor(0.15f, 0.50f, 0.95f);

    // base
    glPushMatrix();
    glTranslatef(0, -0.45f, 0);
    drawBox(0.9f, 0.2f, 0.8f);
    glPopMatrix();

    // respaldo
    glPushMatrix();
    glTranslatef(0, 0.05f, -0.32f);
    drawBox(0.9f, 0.9f, 0.10f);
    glPopMatrix();

    // =====================
    // BARRA DE SEGURIDAD
    // =====================
    setColor(0.85f, 0.85f, 0.85f);

    glPushMatrix();
    glTranslatef(0, 0.35f, 0.15f);
    drawBox(0.75f, 0.08f, 0.08f);
    glPopMatrix();

    // =====================
    // CABEZALES
    // =====================
    setColor(0.05f, 0.15f, 0.50f);

    glPushMatrix();
    glTranslatef(-0.25f, 0.55f, -0.32f);
    drawBox(0.18f, 0.18f, 0.12f);
    glPopMatrix();

    glPushMatrix();
    glTranslatef(0.25f, 0.55f, -0.32f);
    drawBox(0.18f, 0.18f, 0.12f);
    glPopMatrix();

    glPopMatrix();
}

glPopMatrix();
    glPopMatrix();
    
    // Bancas Zona B - bordes de la vereda derecha
    drawBenchAt(20.0f, 18.0f,  90.0f);
    drawBenchAt(29.0f, 18.0f, -90.0f);
    drawBenchAt(20.0f, 30.0f,  90.0f);
    drawBenchAt(29.0f, 30.0f, -90.0f);
    drawBenchAt(20.0f, 42.0f,  90.0f);
    
    // Árboles Zona B - MÁS ÁRBOLES (fuera del juego y lejos de la entrada)
    float treeB_X[6] = {15.0f, 15.0f, 10.0f, 22.0f, 8.0f, 50.0f};
    float treeB_Z[6] = {8.0f, 42.0f, 20.0f, 5.0f, 45.0f, 15.0f};
    for (int k = 0; k < 6; ++k) {
        glPushMatrix();
        glTranslatef(treeB_X[k], 0, treeB_Z[k]);
        drawTree();
        glPopMatrix();
    }

    
    // Postes de luz Zona B
    float lightB_X[4] = {15.0f, 35.0f, 15.0f, 35.0f};
    float lightB_Z[4] = {20.0f, 20.0f, 35.0f, 35.0f};
    for (int k = 0; k < 4; ++k) {
        glPushMatrix();
        glTranslatef(lightB_X[k], 0, lightB_Z[k]);
        drawStreetLight();
        glPopMatrix();
    }
}

// ---------------------------------------------
//  ZONA C: SILLAS VOLADORAS PRO
// ---------------------------------------------
static void drawFlyingChairs() {
    const float CX = -42.0f, CZ = 28.0f; // Más a la derecha y hacia delante 
    glPushMatrix();
    glTranslatef(CX, 0.3f, CZ);

    setColor(0.2f, 0.2f, 0.2f);
    glPushMatrix(); glTranslatef(0, 0.1f, 0); glRotatef(-90, 1, 0, 0); gluDisk(gQuad, 0, 9.0f, 32, 1); glPopMatrix();
    setColor(0.4f, 0.4f, 0.4f);
    glPushMatrix(); glTranslatef(0, 0.3f, 0); glRotatef(-90, 1, 0, 0); gluCylinder(gQuad, 7.0f, 6.0f, 1.5f, 32, 1); glPopMatrix();

    setColor(0.8f, 0.1f, 0.1f);
    glPushMatrix(); glTranslatef(0, 1.8f, 0); drawBox(1.8f, 22.0f, 1.8f); glPopMatrix();
    
    // Estructura de soporte que conecta el palo con la sombrilla
    setColor(0.6f, 0.6f, 0.6f);
    for (int i = 0; i < 8; i++) {
        float angle = i * 45.0f;
        float x = 0.9f * cos(angle * M_PI / 180.0f); // Conectar desde la superficie del palo
        float z = 0.9f * sin(angle * M_PI / 180.0f);
        
        // Brazos que se extienden desde el palo hacia afuera
        glPushMatrix();
        glTranslatef(x, 19.0f, z);
        
        // Calcular ángulo hacia el exterior
        float targetX = 6.5f * cos(angle * M_PI / 180.0f);
        float targetZ = 6.5f * sin(angle * M_PI / 180.0f);
        float dx = targetX - x;
        float dz = targetZ - z;
        float armAngle = atan2(dx, dz) * 180.0f / M_PI;
        
        glRotatef(armAngle, 0, 1, 0);
        glRotatef(-25.0f, 1, 0, 0); // Inclinación hacia arriba
        drawBox(0.5f, 7.0f, 0.5f);
        glPopMatrix();
    }
    
    // Anillo de soporte que conecta con la sombrilla
    setColor(0.5f, 0.5f, 0.5f);
    glPushMatrix(); glTranslatef(0, 23.0f, 0); drawCylinderY(6.0f, 0.6f, 16, 1); glPopMatrix();
    
    glTranslatef(0, 23.8f, 0);
    glRotatef(chairsAngle, 0, 1, 0);
    
    for(int j=0; j<16; j++) {
        glPushMatrix();
        glRotatef(j * 22.5f, 0, 1, 0);
        if(j % 2 == 0) setColor(1.0f, 1.0f, 1.0f);
        else setColor(0.9f, 0.1f, 0.1f);
        
        glBegin(GL_TRIANGLES);
            glVertex3f(0, 5.0f, 0);
            float r1 = 8.5f, a1 = 0, a2 = 23.5f * (M_PI/180.0f);
            glVertex3f(r1 * cos(a1), 0, r1 * sin(a1));
            glVertex3f(r1 * cos(a2), 0, r1 * sin(a2));
        glEnd();
        
        glDisable(GL_LIGHTING);
        if((int)(chairsAngle/15) % 2 == 0) setColor(1, 1, 0);
        else setColor(1, 0.5, 0);
        glPushMatrix(); glTranslatef(8.5f, 0, 0); glutSolidSphere(0.3f, 8, 8); glPopMatrix();
        glEnable(GL_LIGHTING);
        glPopMatrix();
    }

    const int NUM_SILLAS = 12;
    for (int i = 0; i < NUM_SILLAS; i++) {
        glPushMatrix();
        glRotatef(i * (360.0f / NUM_SILLAS), 0, 1, 0);
        glTranslatef(7.8f, 0, 0);
        glRotatef(-currentSwing, 0, 0, 1); 

        glDisable(GL_LIGHTING);
        setColor(0.1f, 0.1f, 0.1f);
        glLineWidth(2.0f);
        glBegin(GL_LINES);
            glVertex3f(0, 0, 0.5f);  glVertex3f(0, -12.5f + chairHeight, 0.5f);
            glVertex3f(0, 0, -0.5f); glVertex3f(0, -12.5f + chairHeight, -0.5f);
        glEnd();
        glEnable(GL_LIGHTING);

        glTranslatef(0, -13.0f + chairHeight, 0);
        glRotatef(currentSwing, 0, 0, 1);
        
        setColor(0.1f, 0.4f, 0.8f);
        drawBox(2.0f, 0.2f, 1.8f);
        setColor(0.0f, 0.2f, 0.5f);
        glPushMatrix(); glTranslatef(0, 0.8f, -0.8f); drawBox(2.0f, 1.4f, 0.2f); glPopMatrix();
        glPopMatrix();
    }
    glPopMatrix();
    
    // Elementos decorativos Zona C (Sillas Voladoras)
    
    
    // Árboles (fuera del juego)
    float treeC_X[4] = {-55.0f, -25.0f, -52.0f, -28.0f};
    float treeC_Z[4] = {20.0f, 22.0f, 38.0f, 38.0f};
    for (int k = 0; k < 4; ++k) {
        glPushMatrix();
        glTranslatef(treeC_X[k], 0, treeC_Z[k]);
        drawTree();
        glPopMatrix();
    }

    
    // Postes de luz
    float lightC_X[2] = {-36.0f, -48.0f};
    float lightC_Z[2] = {26.0f, 30.0f};
    for (int k = 0; k < 2; ++k) {
        glPushMatrix();
        glTranslatef(lightC_X[k], 0, lightC_Z[k]);
        drawStreetLight();
        glPopMatrix();
    }
}
//-------------------
//MONTAÑA RUSA
//----------------
void getCoasterPos(float t, float &x, float &y, float &z) {
    float radius = 18.0f;
    float centerX = 35.0f;
    float centerZ = -35.0f;
    x = centerX + radius * cos(t);
    z = centerZ + radius * sin(t);
    y = 12.0f + 8.0f * sin(3.0f * t);
}

static void drawRollerCoaster() {
    const int segments = 120;
    float x, y, z, nx, ny, nz;

    // Vias
    glLineWidth(4.0f);
    for (int i = 0; i < segments; i++) {
        float t1 = (float)i * 2.0f * M_PI / segments;
        float t2 = (float)(i + 1) * 2.0f * M_PI / segments;
        getCoasterPos(t1, x, y, z); getCoasterPos(t2, nx, ny, nz);

        float r = 0.5f + 0.5f * sin(t1);
        float g = 0.5f + 0.5f * sin(t1 + 2.0f);
        float b = 0.5f + 0.5f * sin(t1 + 4.0f);
        setColor(r, g, b); 
        glBegin(GL_LINES); glVertex3f(x, y, z); glVertex3f(nx, ny, nz); glEnd();

        if(i % 6 == 0) {
            setColor(0.1f, 0.7f, 0.8f);
            glPushMatrix(); glTranslatef(x, y, z); glRotatef(90, 1, 0, 0); gluCylinder(gQuad, 0.5, 0.8, y, 12, 1); glPopMatrix();
        }
        if(i % 2 == 0) {
            setColor(0.95f, 0.85f, 0.1f);
            glPushMatrix(); glTranslatef(x, y, z);
            float dx = nx - x; float dz = nz - z;
            float angle = atan2(dx, dz) * 180.0f / M_PI;
            glRotatef(angle, 0, 1, 0); drawBox(3.0f, 0.2f, 0.5f); glPopMatrix();
        }
    }

    // Tren
    const int numCarts = 3;
    for(int c = 0; c < numCarts; c++) {
        float cartT = coasterT - (c * 0.18f); 
        if (cartT < 0) cartT += 2.0f * M_PI;
        float cx, cy, cz, ncx, ncy, ncz;
        getCoasterPos(cartT, cx, cy, cz); getCoasterPos(cartT + 0.05f, ncx, ncy, ncz); 
        
        glPushMatrix(); glTranslatef(cx, cy + 1.2f, cz);
        float dx = ncx - cx, dz = ncz - cz, dy = ncy - cy;
        float yaw = atan2(dx, dz) * 180.0f / M_PI;
        glRotatef(yaw, 0, 1, 0);
        float dist = sqrt(dx*dx + dz*dz);
        float pitch = atan2(dy, dist) * -180.0f / M_PI;
        glRotatef(pitch, 1, 0, 0);

        if (c == 0) setColor(0.9f, 0.1f, 0.2f); else setColor(0.1f, 0.4f, 0.9f);
        drawBox(2.2f, 1.2f, 2.6f);
        setColor(0.9f, 0.9f, 0.9f); glPushMatrix(); drawBox(2.3f, 0.3f, 2.7f); glPopMatrix();
        setColor(0.1f, 0.1f, 0.15f);
        glPushMatrix(); glTranslatef(0, 0.7f,  0.5f); drawBox(1.6f, 0.8f, 0.6f); glPopMatrix();
        glPushMatrix(); glTranslatef(0, 0.7f, -0.5f); drawBox(1.6f, 0.8f, 0.6f); glPopMatrix();
        glPopMatrix();
    }
}

// ---------------------------------------------
//  ZONA E: RESERVADA
// ---------------------------------------------
static void drawReservedZone(float cx, float cz) {
    setColor(0.50f, 0.50f, 0.48f);
    glPushMatrix(); glTranslatef(cx, 0.15f, cz); drawBox(14.0f, 0.3f, 14.0f); glPopMatrix();
    setColor(0.35f, 0.35f, 0.35f);
    glPushMatrix(); glTranslatef(cx, 0, cz); drawCylinderY(0.15f, 5.0f, 8, 1);
    setColor(0.10f, 0.10f, 0.55f); glTranslatef(0, 5.8f, 0); drawBox(3.5f, 1.2f, 0.15f); glPopMatrix();
    setColor(0.95f, 0.60f, 0.05f);
    float hc = 6.0f;
    float coneOX[4] = {-hc, -hc,  hc,  hc};
    float coneOZ[4] = {-hc,  hc, -hc,  hc};
    for (int ci = 0; ci < 4; ++ci) {
        glPushMatrix(); glTranslatef(cx + coneOX[ci], 0, cz + coneOZ[ci]);
        glRotatef(-90, 1, 0, 0); gluCylinder(gQuad, 0.4f, 0.0f, 1.2f, 8, 4); glPopMatrix();
    }
}

// ---------------------------------------------
//  CARROS CHOCONES - FUNCIONES DE DIBUJO
// ---------------------------------------------
static void drawBumperCarsFloor(){
    float size = 1.0f;
    glDisable(GL_LIGHTING);
    
    // Elevar el piso para que esté sobre el terreno
    glPushMatrix();
    glTranslatef(0, 0.1f, 0);
    
    for(int x=-14;x<14;x++){
        for(int z=-14;z<14;z++){
            float cx = (x + 0.5f) * size;
            float cz = (z + 0.5f) * size;
            float dist = sqrt(cx*cx + cz*cz);
            if(dist > BUMPER_ARENA_RADIUS) continue;
            
            if((x+z)%2==0)
                glColor3f(0.2,0.2,0.2);
            else
                glColor3f(0.85,0.85,0.85);
                
            glBegin(GL_QUADS);
                glVertex3f(x*size,0,z*size);
                glVertex3f((x+1)*size,0,z*size);
                glVertex3f((x+1)*size,0,(z+1)*size);
                glVertex3f(x*size,0,(z+1)*size);
            glEnd();
        }
    }
    
    glPopMatrix();
    glEnable(GL_LIGHTING);
    
    glColor3f(0.1,0.1,0.1);
    glBegin(GL_LINE_LOOP);
    for(int i=0;i<360;i+=5){
        float a=i*M_PI/180;
        glVertex3f(cos(a)*BUMPER_ARENA_RADIUS,0.1f,sin(a)*BUMPER_ARENA_RADIUS);
    }
    glEnd();
}

static void drawBumperCarsPosts(){
    glDisable(GL_LIGHTING);
    float h = 2.6f;
    Color palette[6] = {
        {1.0f, 0.2f, 0.2f},  // rojo
        {1.0f, 0.85f, 0.1f}, // amarillo
        {0.2f, 0.6f, 1.0f},  // azul
        {0.2f, 1.0f, 0.4f},  // verde
        {0.8f, 0.2f, 1.0f},  // morado
        {1.0f, 0.5f, 0.1f}   // naranja
    };
    
    int idx = 0;
    for(int i=0;i<360;i+=15){
        float a = i * M_PI / 180;
        float x = cos(a) * BUMPER_ARENA_RADIUS;
        float z = sin(a) * BUMPER_ARENA_RADIUS;
        Color c = palette[idx % 6];
        idx++;
        
        glPushMatrix();
        glTranslatef(x,0,z);
        glColor3f(c.r,c.g,c.b);
        glRotatef(-90,1,0,0);
        gluCylinder(gQuad, 0.12, 0.12, h, 12, 2);
        glTranslatef(0,0,h);
        glutSolidSphere(0.16,12,12);
        glPopMatrix();
    }
    glEnable(GL_LIGHTING);
}

static void drawBumperCarsMesh(){
    glDisable(GL_LIGHTING);
    glColor3f(0.9f,0.9f,0.9f);
    
    int step = 15;
    float heights[3] = {0.6f, 1.5f, 2.4f};
    
    for(int h=0; h<3; h++){
        glBegin(GL_LINE_LOOP);
        for(int i=0;i<=360;i+=step){
            float a = i * M_PI / 180;
            float x = cos(a) * BUMPER_ARENA_RADIUS;
            float z = sin(a) * BUMPER_ARENA_RADIUS;
            glVertex3f(x, heights[h], z);
        }
        glEnd();
    }
    glEnable(GL_LIGHTING);
}

static void drawBumperCar(BumperCar &c){
    glPushMatrix();
    mat(c.c.r,c.c.g,c.c.b);
    
    // Base - un poco más grande
    glPushMatrix();
    glTranslatef(0,0.25,0);
    glScalef(3.2,0.8,2.5);  // Un poco más grande
    glutSolidCube(1);
    glPopMatrix();
    
    // Carrocería - un poco más grande
    glPushMatrix();
    mat(c.c.r*0.9f,c.c.g*0.9f,c.c.b*0.9f);
    glTranslatef(0,1.2,0);  // Más altura
    glScalef(2.7,0.9,2.0);  // Un poco más grande
    glutSolidCube(1);
    glPopMatrix();
    
    // Cabina - un poco más grande
    glPushMatrix();
    mat(0.75,0.75,0.8);
    glTranslatef(0,1.7,0);  // Más altura
    glScalef(1.6,0.6,1.6);  // Un poco más grande
    glutSolidCube(1);
    glPopMatrix();
    
    // Ruedas - un poco más grandes
    mat(0.05,0.05,0.05);
    float wx[4]={1.3,-1.3,1.3,-1.3};  // Un poco más separadas
    float wz[4]={0.9,0.9,-0.9,-0.9};  // Un poco más separadas
    
    for(int i=0;i<4;i++){
        glPushMatrix();
        glTranslatef(wx[i],0.25,wz[i]);  // Un poco más altura
        glRotatef(90,1,0,0);
        glutSolidTorus(0.12,0.35,10,14);  // Un poco más grandes
        glPopMatrix();
    }
    
    // Luz - un poco más grande
    mat(1,1,0.4);
    glPushMatrix();
    glTranslatef(0,1.9,0);  // Un poco más altura
    glutSolidSphere(0.18,10,10);  // Un poco más grande
    glPopMatrix();
    
    glPopMatrix();
}

static void drawBumperCarsParticles(){
    glDisable(GL_LIGHTING);
    glBegin(GL_POINTS);
    for(auto &p:bumperParticles){
        glColor4f(p.c.r,p.c.g,p.c.b,p.life);
        glVertex3f(p.x,p.y,p.z);
    }
    glEnd();
    glEnable(GL_LIGHTING);
}

// ---------------------------------------------
//  ZONA G: CARROS CHOCONES
// ---------------------------------------------
static void drawBumperCarsArena() {
    const float CX = -10.0f, CZ = 25.0f; // Al lado derecho de las sillas voladoras
    glPushMatrix();
    glTranslatef(CX, 0, CZ);
    
    drawBumperCarsFloor();
    drawBumperCarsMesh();
    drawBumperCarsPosts();
    
    for(int i=0; i<bumperCars.size(); i++){
        glPushMatrix();
        glTranslatef(bumperCars[i].x,0,bumperCars[i].z);
        glRotatef(bumperCars[i].angle,0,1,0);
        
        // Si este es el carro controlado, agregar indicación visual
        if (carControlled && selectedCar == i) {
            // Anillo de selección alrededor del carro
            glDisable(GL_LIGHTING);
            glColor3f(1.0f, 1.0f, 0.0f); // Amarillo brillante
            glLineWidth(3.0f);
            glBegin(GL_LINE_LOOP);
            for(int j=0; j<32; j++){
                float angle = 2.0f * M_PI * j / 32.0f;
                glVertex3f(cos(angle) * 2.5f, 0.5f, sin(angle) * 2.5f);
            }
            glEnd();
            glEnable(GL_LIGHTING);
        }
        
        drawBumperCar(bumperCars[i]);
        glPopMatrix();
    }
    
    drawBumperCarsParticles();
    glPopMatrix();
    
    // Elementos decorativos Zona G (Carritos Chocones) - FUERA de la arena
    // Bancas (muy lejos del centro, fuera del radio 14)
    drawBenchAt(-18.0f,  4.5f, 180.0f);
    drawBenchAt(-10.0f,  4.5f, 180.0f);
    drawBenchAt(  8.0f,  4.5f, 180.0f);
    
    // Árboles (muy lejos del juego)
    float treeG_X[4] = {-30.0f, 15.0f, -35.0f, 12.0f};
    float treeG_Z[4] = {0.0f, 5.0f, 20.0f, 18.0f};
    
    for (int k = 0; k < 4; ++k) {
        glPushMatrix();
        glTranslatef(treeG_X[k], 0, treeG_Z[k]);
        drawTree();
        glPopMatrix();
    }
    
    // Postes de luz (muy lejos del juego)
    float lightG_X[2] = {-25.0f, 10.0f};
    float lightG_Z[2] = {12.0f, 12.0f};
    for (int k = 0; k < 2; ++k) {
        glPushMatrix();
        glTranslatef(lightG_X[k], 0, lightG_Z[k]);
        drawStreetLight();
        glPopMatrix();
    }
}

static void handleBumperCollision(BumperCar &a, BumperCar &b){

    float dx=b.x-a.x;
    float dz=b.z-a.z;
    float dist=sqrt(dx*dx+dz*dz);

    float minD=1.5f;

    if(dist<minD){
        if(dist <= 0.001f) return;
        
        float nx=dx/dist;
        float nz=dz/dist;
        float overlap=minD-dist;

        a.x-=nx*overlap*0.5f;
        a.z-=nz*overlap*0.5f;
        b.x+=nx*overlap*0.5f;
        b.z+=nz*overlap*0.5f;

        // Mantener velocidad constante, solo intercambiar direcciones
        float tempVx = a.vx;
        float tempVz = a.vz;
        
        a.vx = b.vx;
        a.vz = b.vz;
        b.vx = tempVx;
        b.vz = tempVz;

        spawnParticle(a.x,a.z,a.c);
    }
}

static void updateBumperCars(){
    for(auto &c:bumperCars){
        // Si este es el carro controlado, no actualizar con velocidad automática
        if (carControlled && selectedCar >= 0 && &c == &bumperCars[selectedCar]) {
            // El carro controlado se mueve solo con mouse, no con velocidad
            continue;
        }
        
        // Carros no controlados se mueven con velocidad automática
        c.x+=c.vx;
        c.z+=c.vz;
        float d=sqrt(c.x*c.x+c.z*c.z);
        if(d>BUMPER_ARENA_RADIUS-1.0f){
            // Rebote normalizando posición
            float scale = (BUMPER_ARENA_RADIUS-1.0f)/d;
            c.x*=scale;
            c.z*=scale;
            // Invertir velocidad para rebote manteniendo magnitud
            c.vx*=-1.0f;
            c.vz*=-1.0f;
            spawnParticle(c.x,c.z,c.c);
        }
    }
    
    // Verificar colisiones entre todos los carros (incluyendo el controlado)
    for(int i=0;i<bumperCars.size();i++){
        for(int j=i+1;j<bumperCars.size();j++){
            handleBumperCollision(bumperCars[i],bumperCars[j]);
        }
    }
}

static void initBumperCars(){
    Color cols[6]={
        {1.0f, 0.1f, 0.1f},   // rojo brillante
        {0.1f, 0.3f, 1.0f},   // azul brillante
        {1.0f, 0.9f, 0.1f},   // amarillo brillante
        {0.1f, 0.9f, 0.3f},   // verde brillante
        {0.9f, 0.1f, 0.9f},   // morado brillante
        {1.0f, 0.5f, 0.1f}    // naranja brillante
    };
    
    // Inicializar semilla para números aleatorios
    srand(time(NULL));
    
    for(int i=0;i<BUMPER_CARS_COUNT;i++){
        BumperCar c;
        c.x = (rand()%20 - 10) * 0.5f;  // Mayor dispersión
        c.z = (rand()%20 - 10) * 0.5f;  // Mayor dispersión
        float a = (rand()%360) * M_PI / 180.0f;
        c.vx = sin(a) * 0.25f;  // Velocidad mucho mayor
        c.vz = cos(a) * 0.25f;  // Velocidad mucho mayor
        c.angle = 0;
        c.c = cols[i];
        bumperCars.push_back(c);
        printf("Carro %d inicializado en (%.2f, %.2f) con velocidad (%.3f, %.3f)\n", 
               i, c.x, c.z, c.vx, c.vz);
    }
}

// ---------------------------------------------
//  ZONA F: GUSANITO
// ---------------------------------------------
static void drawWormRide() {
    const float CX = 40.0f, CZ = 40.0f; // Más a la derecha y un poco más atrás
    glPushMatrix();
    glTranslatef(CX, 0, CZ);

    // Parte giratoria
    glPushMatrix();
    glRotatef(wormAngle, 0, 1, 0);

    // Plataforma
    setColor(0.85f, 0.05f, 0.10f);
    glPushMatrix(); glTranslatef(0, 0.55f, 0); glRotatef(-90, 1, 0, 0); gluCylinder(gQuad, 6.5f, 6.5f, 0.8f, 70, 1); glPopMatrix();
    
    // Borde dorado
    setColor(1.0f, 0.8f, 0.0f);
    glPushMatrix(); glTranslatef(0, 1.35f, 0); glutSolidTorus(0.12f, 6.1f, 25, 80); glPopMatrix();

    // Poste central
    setColor(1.0f, 0.85f, 0.0f);
    glPushMatrix(); glTranslatef(0, 1.35f, 0); glRotatef(-90, 1, 0, 0); gluCylinder(gQuad, 0.55f, 0.55f, 4.8f, 40, 1); glPopMatrix();

    // Techo
    setColor(1.0f, 0.1f, 0.1f);
    glPushMatrix(); glTranslatef(0, 5.25f, 0); glutSolidTorus(0.18f, 5.2f, 20, 80); glPopMatrix();
    
    setColor(0.10f, 0.25f, 0.95f);
    glPushMatrix(); glTranslatef(0, 6.2f, 0); glRotatef(-90, 1, 0, 0); glutSolidCone(5.8f, 2.3f, 70, 70); glPopMatrix();
    
    setColor(1.0f, 0.8f, 0.0f);
    glPushMatrix(); glTranslatef(0, 7.1f, 0); glScalef(0.8f, 0.35f, 0.8f); glutSolidCube(1.0f); glPopMatrix();

    // Postes del techo
    setColor(0.88f, 0.88f, 0.88f);
    for (int i = 0; i < 8; i++) {
        float ang = i * 2.0f * M_PI / 8;
        float x = cos(ang) * 5.2f;
        float z = sin(ang) * 5.2f;
        glPushMatrix(); glTranslatef(x, 1.35f, z); glRotatef(-90, 1, 0, 0); gluCylinder(gQuad, 0.07f, 0.07f, 3.9f, 20, 1); glPopMatrix();
    }

    // Carros del gusanito
    const int cantidad = 5;
    const float radio = 4.15f;
    for (int i = 0; i < cantidad; i++) {
        float ang = i * 360.0f / cantidad;
        float rad = ang * M_PI / 180.0f;
        float x = cos(rad) * radio;
        float z = sin(rad) * radio;

        glPushMatrix();
        glTranslatef(x, 1.95f, z);
        glRotatef(-ang, 0, 1, 0);

        // Tubo de soporte
        setColor(0.90f, 0.90f, 0.90f);
        glPushMatrix(); glTranslatef(0, 0.45f, 0); glRotatef(-90, 1, 0, 0); gluCylinder(gQuad, 0.06f, 0.06f, 2.85f, 20, 1); glPopMatrix();

        // Gusanito
        setColor(0.10f, 0.85f, 0.20f);
        glPushMatrix(); glTranslatef(-1.0f, 0.15f, 0); glutSolidSphere(0.75f, 30, 30); glPopMatrix();
        glPushMatrix(); glScalef(1.15f, 0.95f, 0.95f); glutSolidSphere(0.75f, 30, 30); glPopMatrix();
        glPushMatrix(); glTranslatef(0.9f, 0.0f, 0); glutSolidSphere(0.68f, 30, 30); glPopMatrix();
        glPushMatrix(); glTranslatef(1.65f, 0.0f, 0); glutSolidSphere(0.60f, 30, 30); glPopMatrix();

        // Antenas
        setColor(0.9f, 0.8f, 0.0f);
        glPushMatrix(); glTranslatef(-1.15f, 0.75f, 0.25f); glRotatef(-35, 0, 0, 1); glRotatef(-90, 1, 0, 0); gluCylinder(gQuad, 0.03f, 0.03f, 0.45f, 12, 1); glPopMatrix();
        glPushMatrix(); glTranslatef(-1.15f, 0.75f, -0.25f); glRotatef(-35, 0, 0, 1); glRotatef(-90, 1, 0, 0); gluCylinder(gQuad, 0.03f, 0.03f, 0.45f, 12, 1); glPopMatrix();

        // Ojos
        setColor(1.0f, 1.0f, 1.0f);
        glPushMatrix(); glTranslatef(-1.45f, 0.35f, 0.25f); glutSolidSphere(0.13f, 20, 20); glPopMatrix();
        glPushMatrix(); glTranslatef(-1.45f, 0.35f, -0.25f); glutSolidSphere(0.13f, 20, 20); glPopMatrix();
        
        setColor(0.0f, 0.0f, 0.0f);
        glPushMatrix(); glTranslatef(-1.53f, 0.35f, 0.25f); glutSolidSphere(0.06f, 15, 15); glPopMatrix();
        glPushMatrix(); glTranslatef(-1.53f, 0.35f, -0.25f); glutSolidSphere(0.06f, 15, 15); glPopMatrix();

        // Asiento
        setColor(1.0f, 0.55f, 0.0f);
        glPushMatrix(); glTranslatef(0.25f, 0.55f, 0); glScalef(1.1f, 0.28f, 0.75f); glutSolidCube(1.0f); glPopMatrix();
        
        // Respaldo
        glPushMatrix(); glTranslatef(0.65f, 0.82f, 0); glRotatef(-20, 0, 0, 1); glScalef(0.18f, 0.65f, 0.75f); glutSolidCube(1.0f); glPopMatrix();
        
        // Barra de seguridad
        setColor(0.75f, 0.75f, 0.75f);
        glPushMatrix(); glTranslatef(0.05f, 0.70f, 0); glScalef(1.0f, 0.05f, 0.05f); glutSolidCube(1.0f); glPopMatrix();

        glPopMatrix();
    }
    glPopMatrix();

    // Cerca externa fija
    glDisable(GL_LIGHTING);
    for (int i = 0; i < 28; i++) {
        float ang = i * 2.0f * M_PI / 28;
        float x = cos(ang) * 8.2f;
        float z = sin(ang) * 8.2f;
        setColor(0.85f, 0.05f, 0.05f);
        glPushMatrix(); glTranslatef(x, 0.8f, z); glScalef(0.22f, 1.6f, 0.22f); glutSolidCube(1.0f); glPopMatrix();
    }
    glEnable(GL_LIGHTING);

    glPopMatrix();
    
    // Árboles (fuera del juego y lejos de la entrada)
    float treeF_X[3] = {25.0f, 55.0f, 28.0f};
    float treeF_Z[3] = {35.0f, 20.0f, 25.0f};
    for (int k = 0; k < 3; ++k) {
        glPushMatrix();
        glTranslatef(treeF_X[k], 0, treeF_Z[k]);
        drawTree();
        glPopMatrix();
    }
    
    // Postes de luz
    float lightF_X[2] = {35.0f, 45.0f};
    float lightF_Z[2] = {35.0f, 45.0f};
    for (int k = 0; k < 2; ++k) {
        glPushMatrix();
        glTranslatef(lightF_X[k], 0, lightF_Z[k]);
        drawStreetLight();
        glPopMatrix();
    }
}

// ---------------------------------------------
//  CONFIGURACION DE LUZ
// ---------------------------------------------
static void setupLighting() {
    // Aplicar iluminación según el modo actual
    switch(currentLighting) {
        case DAY:
            setupDayLighting();
            break;
        case AFTERNOON:
            setupAfternoonLighting();
            break;
        case NIGHT:
            setupNightLighting();
            break;
    }
}

static void drawRoad() {
    float outer = 80.0f;
    float inner = 62.0f;

    glDisable(GL_LIGHTING);
    setColor(0.15f,0.15f,0.15f);

    glBegin(GL_QUADS);

    // arriba
    glVertex3f(-outer,0.02f,-outer);
    glVertex3f( outer,0.02f,-outer);
    glVertex3f( outer,0.02f,-inner);
    glVertex3f(-outer,0.02f,-inner);

    // abajo
    glVertex3f(-outer,0.02f,inner);
    glVertex3f( outer,0.02f,inner);
    glVertex3f( outer,0.02f,outer);
    glVertex3f(-outer,0.02f,outer);

    // izquierda
    glVertex3f(-outer,0.02f,-inner);
    glVertex3f(-inner,0.02f,-inner);
    glVertex3f(-inner,0.02f,inner);
    glVertex3f(-outer,0.02f,inner);

    // derecha
    glVertex3f(inner,0.02f,-inner);
    glVertex3f(outer,0.02f,-inner);
    glVertex3f(outer,0.02f,inner);
    glVertex3f(inner,0.02f,inner);

    glEnd();
    glEnable(GL_LIGHTING);
}

static void drawSidewalk() {
    float outer = 62.0f;
    float inner = 55.0f;

    glDisable(GL_LIGHTING);
    setColor(0.7f,0.7f,0.7f);

    glBegin(GL_QUADS);

    glVertex3f(-outer,0.01f,-outer);
    glVertex3f( outer,0.01f,-outer);
    glVertex3f( outer,0.01f,-inner);
    glVertex3f(-outer,0.01f,-inner);

    glVertex3f(-outer,0.01f,inner);
    glVertex3f( outer,0.01f,inner);
    glVertex3f( outer,0.01f,outer);
    glVertex3f(-outer,0.01f,outer);

    glVertex3f(-outer,0.01f,-inner);
    glVertex3f(-inner,0.01f,-inner);
    glVertex3f(-inner,0.01f,inner);
    glVertex3f(-outer,0.01f,inner);

    glVertex3f(inner,0.01f,-inner);
    glVertex3f(outer,0.01f,-inner);
    glVertex3f(outer,0.01f,inner);
    glVertex3f(inner,0.01f,inner);

    glEnd();
    glEnable(GL_LIGHTING);
}

void drawHouse() {

    // =========================
    // BASE
    // =========================
    setColor(0.85f, 0.75f, 0.60f);

    glPushMatrix();
    glTranslatef(0, 1.5f, 0);
    drawBox(6.0f, 3.0f, 6.0f);
    glPopMatrix();

    // =========================
    // TECHO REAL
    // =========================
    setColor(0.7f, 0.1f, 0.1f);

    glBegin(GL_TRIANGLES);

    // Frente
    glVertex3f(-3.2f, 3.0f, 3.0f);
    glVertex3f( 3.2f, 3.0f, 3.0f);
    glVertex3f( 0.0f, 5.0f, 3.0f);

    // Atrás
    glVertex3f(-3.2f, 3.0f, -3.0f);
    glVertex3f( 3.2f, 3.0f, -3.0f);
    glVertex3f( 0.0f, 5.0f, -3.0f);

    glEnd();

    // Laterales del techo
    glBegin(GL_QUADS);

    // Izquierda
    glVertex3f(-3.2f, 3.0f, -3.0f);
    glVertex3f(0.0f, 5.0f, -3.0f);
    glVertex3f(0.0f, 5.0f, 3.0f);
    glVertex3f(-3.2f, 3.0f, 3.0f);
    // Derecha
    glVertex3f(3.2f, 3.0f, -3.0f);
    glVertex3f(0.0f, 5.0f, -3.0f);
    glVertex3f(0.0f, 5.0f, 3.0f);
    glVertex3f(3.2f, 3.0f, 3.0f);

    glEnd();

    // =========================
    // PUERTA
    // =========================
    setColor(0.35f, 0.2f, 0.1f);

    glPushMatrix();
    glTranslatef(0, 0.9f, 3.05f);
    drawBox(1.2f, 1.8f, 0.15f);
    glPopMatrix();
    // =========================
    // VENTANAS
    // =========================
    setColor(0.5f, 0.8f, 1.0f);

    glPushMatrix();
    glTranslatef(-1.7f, 2.0f, 3.05f);
    drawBox(1.0f, 1.0f, 0.1f);
    glPopMatrix();

    glPushMatrix();
    glTranslatef(1.7f, 2.0f, 3.05f);
    drawBox(1.0f, 1.0f, 0.1f);
    glPopMatrix();

    // =========================
    // CHIMENEA
    // =========================
    setColor(0.4f, 0.3f, 0.3f);

    glPushMatrix();
    glTranslatef(-1.5f, 5.0f, 0.0f);
    drawBox(0.7f, 1.8f, 0.7f);
    glPopMatrix();
}

static void drawParkingLot() {
    // Superficie principal del estacionamiento
    setColor(0.25f,0.25f,0.25f);
    glPushMatrix();
    glTranslatef(40,0.0f,75);
    drawBox(40,0.1f,20);
    glPopMatrix();

    // Acera/bordillo alrededor
    setColor(0.6f,0.6f,0.6f);
    glPushMatrix();
    glTranslatef(40,0.0f,75);
    drawBox(42,0.1f,22);
    glPopMatrix();

    glDisable(GL_LIGHTING);

    // Líneas de estacionamiento blancas
    setColor(1,1,1);
    glLineWidth(3.0f);
    
    // Líneas longitudinales (espacios para carros)
    for(int i=-18;i<=18;i+=8){
        glBegin(GL_LINES);
        glVertex3f(i+40,0.2f,65);
        glVertex3f(i+40,0.2f,85);
        glEnd();
    }
    
    // Líneas transversales (separación de espacios)
    for(int j=65;j<=85;j+=5){
        glBegin(GL_LINES);
        glVertex3f(22,0.2f,j);
        glVertex3f(58,0.2f,j);
        glEnd();
    }

    // Marca de entrada/salida
    setColor(1.0f,1.0f,0.0f);
    glLineWidth(4.0f);
    glBegin(GL_LINES);
    glVertex3f(40,0.2f,85);
    glVertex3f(40,0.2f,65);
    glEnd();

    glEnable(GL_LIGHTING);
}

void carro(float x, float y, float z)
{
    glPushMatrix();
    glTranslatef(x, y, z);

    // CARROCERÍA
    glColor3f(0.1, 0.3, 1.0); // azul
    glPushMatrix();
    glScalef(3.0, 0.8, 1.5);
    glutSolidCube(1);
    glPopMatrix();

    // TECHO
    glColor3f(0.0, 0.2, 0.8);
    glPushMatrix();
    glTranslatef(0, 0.7, 0);
    glScalef(1.8, 0.7, 1.3);
    glutSolidCube(1);
    glPopMatrix();

    // VIDRIO DELANTERO
    glColor3f(0.5, 0.8, 1.0);
    glPushMatrix();
    glTranslatef(0.7, 0.75, 0);
    glScalef(0.3, 0.5, 1.1);
    glutSolidCube(1);
    glPopMatrix();

    // VIDRIO TRASERO
    glPushMatrix();
    glTranslatef(-0.7, 0.75, 0);
    glScalef(0.3, 0.5, 1.1);
    glutSolidCube(1);
    glPopMatrix();

    // RUEDAS
    glColor3f(0,0,0);

    // rueda 1
    glPushMatrix();
    glTranslatef(1.0, -0.5, 0.8);
    glutSolidTorus(0.1, 0.3, 20, 20);
    glPopMatrix();

    // rueda 2
    glPushMatrix();
    glTranslatef(-1.0, -0.5, 0.8);
    glutSolidTorus(0.1, 0.3, 20, 20);
    glPopMatrix();

    // rueda 3
    glPushMatrix();
    glTranslatef(1.0, -0.5, -0.8);
    glutSolidTorus(0.1, 0.3, 20, 20);
    glPopMatrix();

    // rueda 4
    glPushMatrix();
    glTranslatef(-1.0, -0.5, -0.8);
    glutSolidTorus(0.1, 0.3, 20, 20);
    glPopMatrix();

    // FAROS
    glColor3f(1,1,0);

    glPushMatrix();
    glTranslatef(1.55, 0, 0.4);
    glutSolidSphere(0.12, 20, 20);
    glPopMatrix();

    glPushMatrix();
    glTranslatef(1.55, 0, -0.4);
    glutSolidSphere(0.12, 20, 20);
    glPopMatrix();

    glPopMatrix();
}

static void drawParkedCars() {
    // Colocar 4 carros en el estacionamiento usando la nueva función carro()
    carro(25, 0.1f, 70);  // Carro 1
    carro(33, 0.1f, 70);  // Carro 2
    carro(41, 0.1f, 70);  // Carro 3
    carro(49, 0.1f, 70);  // Carro 4
}

static void drawSkyDay() {
    glDisable(GL_LIGHTING);
    
    // Cielo azul claro
    glColor3f(0.53f, 0.81f, 0.92f);
    glBegin(GL_QUADS);
    glVertex3f(-200, 0, -200);
    glVertex3f(200, 0, -200);
    glVertex3f(200, 150, -200);
    glVertex3f(-200, 150, -200);
    glEnd();
    
    glBegin(GL_QUADS);
    glVertex3f(-200, 0, 200);
    glVertex3f(200, 0, 200);
    glVertex3f(200, 150, 200);
    glVertex3f(-200, 150, 200);
    glEnd();
    
    glBegin(GL_QUADS);
    glVertex3f(-200, 0, -200);
    glVertex3f(-200, 0, 200);
    glVertex3f(-200, 150, 200);
    glVertex3f(-200, 150, -200);
    glEnd();
    
    glBegin(GL_QUADS);
    glVertex3f(200, 0, -200);
    glVertex3f(200, 0, 200);
    glVertex3f(200, 150, 200);
    glVertex3f(200, 150, -200);
    glEnd();
    
    // Sol amarillo brillante
    glColor3f(1.0f, 0.95f, 0.2f);
    glPushMatrix();
    glTranslatef(80.0f, 100.0f, -150.0f);
    glutSolidSphere(15.0f, 32, 32);
    glPopMatrix();
    
    // Nubes blancas
    glColor3f(1.0f, 1.0f, 1.0f);
    float cloudPositions[][3] = {
        {-50.0f, 90.0f, -120.0f},
        {30.0f, 85.0f, -100.0f},
        {-20.0f, 95.0f, -80.0f},
        {60.0f, 88.0f, -110.0f},
        {-80.0f, 92.0f, -90.0f}
    };
    
    for(int i = 0; i < 5; i++) {
        glPushMatrix();
        glTranslatef(cloudPositions[i][0], cloudPositions[i][1], cloudPositions[i][2]);
        glutSolidSphere(8.0f, 16, 16);
        glTranslatef(10.0f, 0.0f, 0.0f);
        glutSolidSphere(7.0f, 16, 16);
        glTranslatef(-15.0f, 2.0f, 0.0f);
        glutSolidSphere(6.0f, 16, 16);
        glPopMatrix();
    }
    
    glEnable(GL_LIGHTING);
}

void drawSkyAfternoon() {
    glDisable(GL_LIGHTING);
    
    // Cielo anaranjado/rojo del atardecer
    glColor3f(0.95f, 0.55f, 0.3f);
    glBegin(GL_QUADS);
    glVertex3f(-200, 0, -200);
    glVertex3f(200, 0, -200);
    glVertex3f(200, 150, -200);
    glVertex3f(-200, 150, -200);
    glEnd();
    
    glBegin(GL_QUADS);
    glVertex3f(-200, 0, 200);
    glVertex3f(200, 0, 200);
    glVertex3f(200, 150, 200);
    glVertex3f(-200, 150, 200);
    glEnd();
    
    glBegin(GL_QUADS);
    glVertex3f(-200, 0, -200);
    glVertex3f(-200, 0, 200);
    glVertex3f(-200, 150, 200);
    glVertex3f(-200, 150, -200);
    glEnd();
    
    glBegin(GL_QUADS);
    glVertex3f(200, 0, -200);
    glVertex3f(200, 0, 200);
    glVertex3f(200, 150, 200);
    glVertex3f(200, 150, -200);
    glEnd();
    
    // Sol anaranjado/rojo del atardecer
    glColor3f(1.0f, 0.6f, 0.2f);
    glPushMatrix();
    glTranslatef(80.0f, 60.0f, -150.0f);
    glutSolidSphere(15.0f, 32, 32);
    glPopMatrix();
    
    // Nubes anaranjadas
    glColor3f(0.9f, 0.7f, 0.5f);
    float cloudPositions[][3] = {
        {-50.0f, 70.0f, -120.0f},
        {30.0f, 65.0f, -100.0f},
        {-20.0f, 75.0f, -80.0f},
        {60.0f, 68.0f, -110.0f},
        {-80.0f, 72.0f, -90.0f}
    };
    
    for(int i = 0; i < 5; i++) {
        glPushMatrix();
        glTranslatef(cloudPositions[i][0], cloudPositions[i][1], cloudPositions[i][2]);
        glutSolidSphere(8.0f, 16, 16);
        glTranslatef(10.0f, 0.0f, 0.0f);
        glutSolidSphere(7.0f, 16, 16);
        glTranslatef(-15.0f, 2.0f, 0.0f);
        glutSolidSphere(6.0f, 16, 16);
        glPopMatrix();
    }
    
    glEnable(GL_LIGHTING);
}

void drawSkyNight() {
    glDisable(GL_LIGHTING);
    
    // Cielo celeste claro nocturno
    glColor3f(0.1f, 0.15f, 0.3f);
    glBegin(GL_QUADS);
    glVertex3f(-200, 0, -200);
    glVertex3f(200, 0, -200);
    glVertex3f(200, 150, -200);
    glVertex3f(-200, 150, -200);
    glEnd();
    
    glBegin(GL_QUADS);
    glVertex3f(-200, 0, 200);
    glVertex3f(200, 0, 200);
    glVertex3f(200, 150, 200);
    glVertex3f(-200, 150, 200);
    glEnd();
    
    glBegin(GL_QUADS);
    glVertex3f(-200, 0, -200);
    glVertex3f(-200, 0, 200);
    glVertex3f(-200, 150, 200);
    glVertex3f(-200, 150, -200);
    glEnd();
    
    glBegin(GL_QUADS);
    glVertex3f(200, 0, -200);
    glVertex3f(200, 0, 200);
    glVertex3f(200, 150, 200);
    glVertex3f(200, 150, -200);
    glEnd();
    
    // Estrellas
    glColor3f(1.0f, 1.0f, 1.0f);
    srand(42);
    for(int i = 0; i < 200; i++) {
        float x = (rand() % 400 - 200);
        float y = (rand() % 100 + 50);
        float z = (rand() % 400 - 200);
        glPushMatrix();
        glTranslatef(x, y, z);
        glutSolidSphere(0.5f, 8, 8);
        glPopMatrix();
    }
    
    // Luna blanca
    glColor3f(0.9f, 0.9f, 1.0f);
    glPushMatrix();
    glTranslatef(80.0f, 100.0f, -150.0f);
    glutSolidSphere(12.0f, 32, 32);
    glPopMatrix();
    
    glEnable(GL_LIGHTING);
}

void drawSky() {
    switch(currentLighting) {
        case DAY:
            drawSkyDay();
            break;
        case AFTERNOON:
            drawSkyAfternoon();
            break;
        case NIGHT:
            drawSkyNight();
            break;
    }
}

void applyCamera() {
    glLoadIdentity();
    float rad   = (float)(M_PI / 180.0);
    float lookX = camX + sinf(camYaw * rad);
    float lookY = camY + sinf(camPit * rad);
    float lookZ = camZ - cosf(camYaw * rad);
    gluLookAt(camX, camY, camZ, lookX, lookY, lookZ, 0.0f, 1.0f, 0.0f);
}

void display() {
    // Cambiar color de fondo según el modo de iluminación
    switch(currentLighting) {
        case DAY:
            glClearColor(0.53f, 0.81f, 0.92f, 1.0f); // Celeste claro
            break;
        case AFTERNOON:
            glClearColor(0.95f, 0.55f, 0.3f, 1.0f); // Anaranjado
            break;
        case NIGHT:
            glClearColor(0.1f, 0.15f, 0.3f, 1.0f); // Azul oscuro nocturno
            break;
    }
    
    glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);
    applyCamera();
    setupLighting();

    drawSky();

    drawRoad();
    drawSidewalk();
    drawParkingLot();
    drawParkedCars();
    
    drawTerrain();
    drawFence();
    drawEntrance();
    drawTicketBooth();
	drawSweetKiosk(10.0f, 5.0f, 180.0f);  
	drawSweetKiosk(19.0f, 25.0f, 90.0f);   
	drawSweetKiosk(40.0f, 5.0f, 180.0f); 
	drawSweetKiosk(-19.0f, -10.0f, 270.0f); 
	drawSmallCircus(-5.0f, -20.0f);

	
    drawFerrisWheel();
    drawDropTower();
    drawFlyingChairs();
    drawRollerCoaster();
    drawWormRide();
    drawBumperCarsArena();

    // Agregar casas alrededor del parque - distribución más natural
    // Frente del parque (con variación en posición)
    float frontPositions[] = {-70, -40, -10, 20, 50};
    for(int i=0; i<5; i++){
        glPushMatrix();
        glTranslatef(frontPositions[i], 0, -95);
        glRotatef((i%2)*180, 0, 1, 0); // Alternar orientación
        drawHouse();
        glPopMatrix();
    }

    // Fondo del parque
    float backPositions[] = {-60, -30, 0, 30, 60};
    for(int i=0; i<5; i++){
        glPushMatrix();
        glTranslatef(backPositions[i], 0, 95);
        glRotatef((i%2)*180, 0, 1, 0);
        drawHouse();
        glPopMatrix();
    }

    // Costado izquierdo - menos casas, más espaciadas
    float leftPositions[] = {-50, -10, 30};
    for(int i=0; i<3; i++){
        glPushMatrix();
        glTranslatef(-95, 0, leftPositions[i]);
        glRotatef(90, 0, 1, 0);
        drawHouse();
        glPopMatrix();
    }

    // Crear plataforma para casas y estacionamiento
    glDisable(GL_LIGHTING);
    setColor(0.3f, 0.25f, 0.2f);
    
    // Plataforma principal para casas
    glBegin(GL_QUADS);
    // Frente
    glVertex3f(-100, 0.0f, -100);
    glVertex3f( 100, 0.0f, -100);
    glVertex3f( 100, 0.0f,  -80);
    glVertex3f(-100, 0.0f,  -80);
    // Fondo
    glVertex3f(-100, 0.0f,  80);
    glVertex3f( 100, 0.0f,  80);
    glVertex3f( 100, 0.0f, 100);
    glVertex3f(-100, 0.0f, 100);
    // Izquierda
    glVertex3f(-100, 0.0f, -80);
    glVertex3f( -80, 0.0f, -80);
    glVertex3f( -80, 0.0f,  80);
    glVertex3f(-100, 0.0f,  80);
    // Derecha
    glVertex3f( 80, 0.0f, -80);
    glVertex3f( 100, 0.0f, -80);
    glVertex3f( 100, 0.0f,  80);
    glVertex3f( 80, 0.0f,  80);
    glEnd();
    glEnable(GL_LIGHTING);

    // Costado derecho - alineadas con entrada (ahora en plataforma)
    float rightPositions[] = {-40, 0, 40};
    for(int i=0; i<3; i++){
        glPushMatrix();
        glTranslatef(95, 0.0f, rightPositions[i]);
        glRotatef(-90, 0, 1, 0);
        drawHouse();
        glPopMatrix();
    }

    // Casas adicionales en esquinas para completar el vecindario (en plataforma)
    glPushMatrix();
    glTranslatef(-85, 0.0f, -85);
    glRotatef(45, 0, 1, 0);
    drawHouse();
    glPopMatrix();

    glPushMatrix();
    glTranslatef(85, 0.0f, -85);
    glRotatef(-45, 0, 1, 0);
    drawHouse();
    glPopMatrix();

    glPushMatrix();
    glTranslatef(-85, 0.0f, 85);
    glRotatef(135, 0, 1, 0);
    drawHouse();
    glPopMatrix();

    glPushMatrix();
    glTranslatef(85, 0.0f, 85);
    glRotatef(-135, 0, 1, 0);
    drawHouse();
    glPopMatrix();

    glutSwapBuffers();
}

void reshape(int w, int h) {
    if (h == 0) h = 1;
    glViewport(0, 0, w, h);
    glMatrixMode(GL_PROJECTION);
    glLoadIdentity();
    gluPerspective(60.0, (double)w / (double)h, 0.1, 300.0);
    glMatrixMode(GL_MODELVIEW);
}

void keyboard(unsigned char key, int x, int y) {
    const float MOVE = 0.5f;
    const float ROT  = 3.0f;
    float rad = camYaw * (float)(M_PI / 180.0f);
    
    // Selección de carros con teclas numéricas
    if (key == '7' && bumperCars.size() > 0) {
        selectedCar = 0;
        carControlled = true;
        printf("Carro 0 seleccionado con tecla 7\n");
    }
    else if (key == '8' && bumperCars.size() > 1) {
        selectedCar = 1;
        carControlled = true;
        printf("Carro 1 seleccionado con tecla 8\n");
    }
    else if (key == '9' && bumperCars.size() > 2) {
        selectedCar = 2;
        carControlled = true;
        printf("Carro 2 seleccionado con tecla 9\n");
    }
    else if (key == '0' && bumperCars.size() > 3) {
        selectedCar = 3;
        carControlled = true;
        printf("Carro 3 seleccionado con tecla 0\n");
    }
    else {
        switch (key) {
        case 'w': case 'W': camX += sinf(rad) * MOVE; camZ -= cosf(rad) * MOVE; break;
        case 's': case 'S': camX -= sinf(rad) * MOVE; camZ += cosf(rad) * MOVE; break;
        case 'a': case 'A': camYaw -= ROT; break;
        case 'd': case 'D': camYaw += ROT; break;
        case 'q': case 'Q': camY += MOVE; break;
        case 'e': case 'E': camY -= MOVE; break;
        case ' ': animEnabled = !animEnabled; break;
        case '1': currentLighting = DAY; printf("Modo: DIA\n"); break;
        case '2': currentLighting = AFTERNOON; printf("Modo: TARDE\n"); break;
        case '3': currentLighting = NIGHT; printf("Modo: NOCHE\n"); break;
        case 't': case 'T': towerManualMode = !towerManualMode; printf("Torre modo manual: %s\n", towerManualMode ? "ACTIVADO" : "DESACTIVADO"); break;
        case 'u': case 'U': if(towerManualMode) { towerPlatformY += towerManualSpeed; if(towerPlatformY > TOWER_HEIGHT_TRAVEL) towerPlatformY = TOWER_HEIGHT_TRAVEL; } break;
        case 'f': case 'F': if(towerManualMode) { towerPlatformY -= towerManualSpeed; if(towerPlatformY < 0) towerPlatformY = 0; } break;
        case 'p': case 'P': if(towerManualMode) printf("Torre detenida en altura: %.2f\n", towerPlatformY); break;
        case '+': case '=': towerManualSpeed += 0.02f; printf("Velocidad manual: %.2f\n", towerManualSpeed); break;
        case '-': case '_': towerManualSpeed -= 0.02f; if(towerManualSpeed < 0.02f) towerManualSpeed = 0.02f; printf("Velocidad manual: %.2f\n", towerManualSpeed); break;
        case 27: if (gQuad) gluDeleteQuadric(gQuad); exit(0);
        }
    }
    glutPostRedisplay();
}

void specialKeys(int key, int x, int y) {
    switch (key) {
    case GLUT_KEY_UP:   camPit += 2.0f; if (camPit >  89.0f) camPit =  89.0f; break;
    case GLUT_KEY_DOWN: camPit -= 2.0f; if (camPit < -89.0f) camPit = -89.0f; break;
    }
    glutPostRedisplay();
}

// ---------------------------------------------
//  FUNCIÓN DE PROCESAMIENTO DEL MENÚ
// ---------------------------------------------
void onMenu(int opcion) {
    // Procesar opciones de Tiempo del día
    switch (opcion) {
    case MENU_DIA:
        currentLighting = DAY;
        printf("Modo: DIA\n");
        setupDayLighting();
        break;
    case MENU_TARDE:
        currentLighting = AFTERNOON;
        printf("Modo: TARDE\n");
        setupAfternoonLighting();
        break;
    case MENU_NOCHE:
        currentLighting = NIGHT;
        printf("Modo: NOCHE\n");
        setupNightLighting();
        break;

    // Procesar opciones de Sonidos
    case MENU_SONIDO_ACTIVAR_TODO:
        sonidoActivado = true;
        sonidoAmbiente = true;
        musicaParque = true;
        printf("Sonidos: ACTIVAR TODO\n");
        break;
    case MENU_SONIDO_DESACTIVAR_TODO:
        sonidoActivado = false;
        sonidoAmbiente = false;
        musicaParque = false;
        musicaActual = 0;
        stopMusic();
        printf("Sonidos: DESACTIVAR TODO\n");
        break;
    case MENU_SONIDO_AMBIENTE:
        if (!sonidoAmbiente) {
            if (!sonidoActivado) sonidoActivado = true;
            sonidoAmbiente = true;
            playMusic(0);
            printf("Sonido ambiente: ACTIVADO\n");
        } else {
            sonidoAmbiente = false;
            stopMusic();
            printf("Sonido ambiente: DESACTIVADO\n");
        }
        break;
    case MENU_MUSICA_PARQUE:
        musicaParque = !musicaParque;
        printf("Música del parque: %s\n", musicaParque ? "ACTIVADA" : "DESACTIVADA");
        break;
    case MENU_SONIDO_BOLETERIA:
        if (!sonidoActivado) sonidoActivado = true;
        musicaActual = 8;
        playMusic(8);
        printf("Sonido: Boletería\n");
        break;
    case MENU_SONIDO_RULETA:
        if (!sonidoActivado) sonidoActivado = true;
        musicaActual = 1;
        playMusic(1);
        printf("Sonido: Ruleta rusa\n");
        break;
    case MENU_SONIDO_SILLAS:
        if (!sonidoActivado) sonidoActivado = true;
        musicaActual = 2;
        playMusic(2);
        printf("Sonido: Sillas voladoras\n");
        break;
    case MENU_SONIDO_CARRITOS:
        if (!sonidoActivado) sonidoActivado = true;
        musicaActual = 3;
        playMusic(3);
        printf("Sonido: Carritos chocones\n");
        break;
    case MENU_SONIDO_MONTANA:
        if (!sonidoActivado) sonidoActivado = true;
        musicaActual = 4;
        playMusic(4);
        printf("Sonido: Montaña rusa\n");
        break;
    case MENU_SONIDO_GUSANITO:
        if (!sonidoActivado) sonidoActivado = true;
        musicaActual = 5;
        playMusic(5);
        printf("Sonido: Gusanito\n");
        break;
    case MENU_SONIDO_TORRE:
        if (!sonidoActivado) sonidoActivado = true;
        musicaActual = 6;
        playMusic(6);
        printf("Sonido: Torre de caída\n");
        break;
    case MENU_SONIDO_CIRCO:
        if (!sonidoActivado) sonidoActivado = true;
        musicaActual = 7;
        playMusic(7);
        printf("Sonido: Circo\n");
        break;

    // Procesar opciones de Atracciones
    case MENU_ATTR_RULETA_RUSA:
        animRuletaRusa = !animRuletaRusa;
        printf("Ruleta rusa: %s\n", animRuletaRusa ? "ACTIVADA" : "DESACTIVADA");
        break;
    case MENU_ATTR_SILLAS_VOLADORAS:
        animSillasVoladoras = !animSillasVoladoras;
        printf("Sillas voladoras: %s\n", animSillasVoladoras ? "ACTIVADAS" : "DESACTIVADAS");
        break;
    case MENU_ATTR_CARRITOS_CHOCONES:
        animCarritosChocones = !animCarritosChocones;
        printf("Carritos chocones: %s\n", animCarritosChocones ? "ACTIVADOS" : "DESACTIVADOS");
        break;
    case MENU_ATTR_MONTANA_RUSA:
        animMontanaRusa = !animMontanaRusa;
        printf("Montaña rusa: %s\n", animMontanaRusa ? "ACTIVADA" : "DESACTIVADA");
        break;
    case MENU_ATTR_GUSANITO:
        animGusanito = !animGusanito;
        printf("Gusanito: %s\n", animGusanito ? "ACTIVADO" : "DESACTIVADO");
        break;
    case MENU_ATTR_TORRE_CAIDA:
        animTorreCaida = !animTorreCaida;
        printf("Torre de caída: %s\n", animTorreCaida ? "ACTIVADA" : "DESACTIVADA");
        break;
    case MENU_ATTR_CIRCO:
        animCirco = !animCirco;
        printf("Circo: %s\n", animCirco ? "ACTIVADO" : "DESACTIVADO");
        break;
    }

    glutPostRedisplay();
}

// ---------------------------------------------
//  FUNCIÓN DE CREACIÓN DEL MENÚ
// ---------------------------------------------
void creacionMenu() {
    // Crear submenú de Tiempo del día
    int menuTiempo = glutCreateMenu(onMenu);
    glutAddMenuEntry("Día", MENU_DIA);
    glutAddMenuEntry("Tarde", MENU_TARDE);
    glutAddMenuEntry("Noche", MENU_NOCHE);

    // Crear submenú de Sonidos
    int menuSonidos = glutCreateMenu(onMenu);
    glutAddMenuEntry("Activar todos los sonidos", MENU_SONIDO_ACTIVAR_TODO);
    glutAddMenuEntry("Desactivar todos los sonidos", MENU_SONIDO_DESACTIVAR_TODO);
    glutAddMenuEntry("---", -1);  // Separador
    glutAddMenuEntry("Sonido ambiente", MENU_SONIDO_AMBIENTE);
    glutAddMenuEntry("Música del parque", MENU_MUSICA_PARQUE);
    glutAddMenuEntry("Boletería", MENU_SONIDO_BOLETERIA);
    glutAddMenuEntry("---", -1);  // Separador
    glutAddMenuEntry("Ruleta rusa", MENU_SONIDO_RULETA);
    glutAddMenuEntry("Sillas voladoras", MENU_SONIDO_SILLAS);
    glutAddMenuEntry("Carritos chocones", MENU_SONIDO_CARRITOS);
    glutAddMenuEntry("Montaña rusa", MENU_SONIDO_MONTANA);
    glutAddMenuEntry("Gusanito", MENU_SONIDO_GUSANITO);
    glutAddMenuEntry("Torre de caída", MENU_SONIDO_TORRE);
    glutAddMenuEntry("Circo", MENU_SONIDO_CIRCO);

    // Crear submenú de Atracciones
    int menuAtracciones = glutCreateMenu(onMenu);
    glutAddMenuEntry("Ruleta rusa", MENU_ATTR_RULETA_RUSA);
    glutAddMenuEntry("Sillas voladoras", MENU_ATTR_SILLAS_VOLADORAS);
    glutAddMenuEntry("Carritos chocones", MENU_ATTR_CARRITOS_CHOCONES);
    glutAddMenuEntry("Montaña rusa", MENU_ATTR_MONTANA_RUSA);
    glutAddMenuEntry("Gusanito", MENU_ATTR_GUSANITO);
    glutAddMenuEntry("Torre de caída", MENU_ATTR_TORRE_CAIDA);
    glutAddMenuEntry("Circo", MENU_ATTR_CIRCO);

    // Crear menú principal
    int menuPrincipal = glutCreateMenu(onMenu);
    glutAddSubMenu("Tiempo del día", menuTiempo);
    glutAddSubMenu("Sonidos", menuSonidos);
    glutAddSubMenu("Atracciones", menuAtracciones);

    // Adjuntar menú al clic derecho
    glutAttachMenu(GLUT_RIGHT_BUTTON);
}

void selectCarAtPosition(int mouseX, int mouseY);

void mouseButton(int button, int state, int x, int y) {
    if (button == GLUT_LEFT_BUTTON) {
        mouseLeftDown = (state == GLUT_DOWN);
        mouseLastX = x;
        mouseLastY = y;

        if (state == GLUT_DOWN) {
            selectCarAtPosition(x, y);

            int currentTime = glutGet(GLUT_ELAPSED_TIME);

            if (currentTime - lastClickTime < 300) {
                selectCarAtPosition(x, y);
            }

            lastClickTime = currentTime;
        }
    }
    else if (button == GLUT_RIGHT_BUTTON) {
        mouseRightDown = (state == GLUT_DOWN);
        mouseLastX = x;
        mouseLastY = y;
    }
    else if (button == 3) {
        float rad = camYaw * (float)(M_PI / 180.0f);
        camX += sinf(rad) * MOUSE_ZOOM_STEP;
        camZ -= cosf(rad) * MOUSE_ZOOM_STEP;
        glutPostRedisplay();
    }
    else if (button == 4) {
        float rad = camYaw * (float)(M_PI / 180.0f);
        camX -= sinf(rad) * MOUSE_ZOOM_STEP;
        camZ += cosf(rad) * MOUSE_ZOOM_STEP;
        glutPostRedisplay();
    }
}

void selectCarAtPosition(int mouseX, int mouseY) {
    const float CX = -10.0f, CZ = 25.0f; // Posición del área de carros chocones
    float minDistance = 999.0f;
    int closestCar = -1;
    
    // Sistema optimizado de selección con precisión mejorada
    for (int i = 0; i < bumperCars.size(); i++) {
        // Posición del carro en coordenadas de mundo
        float carWorldX = CX + bumperCars[i].x;
        float carWorldZ = CZ + bumperCars[i].z;
        
        // Cálculo de distancia directa y eficiente
        float dx = carWorldX - camX;
        float dz = carWorldZ - camZ;
        float distance3D = sqrt(dx*dx + dz*dz);
        
        // Proyección a pantalla con sensibilidad ajustada
        float screenX = 400 + dx * 15.0f; // Sensibilidad optimizada
        float screenY = 300 - dz * 15.0f;
        
        // Distancia 2D en pantalla
        float screenDistance = sqrt((screenX - mouseX) * (screenX - mouseX) + 
                                  (screenY - mouseY) * (screenY - mouseY));
        
        // Umbral adaptativo basado en distancia 3D
        float adaptiveThreshold = 80.0f + distance3D * 2.0f;
        
        // Selección más precisa: considerar distancia 3D y 2D
        if (screenDistance < minDistance && screenDistance < adaptiveThreshold) {
            // Priorizar carros más cercanos en 3D
            float priority = screenDistance + distance3D * 0.3f;
            if (priority < minDistance) {
                minDistance = priority;
                closestCar = i;
            }
        }
    }
    
    if (closestCar != -1) {
        selectedCar = closestCar;
        carControlled = true;
        printf("Carro %d seleccionado con mouse (distancia: %.1f)\n", closestCar, minDistance);
    } else {
        // Solo deseleccionar si no hay carro cercano
        if (carControlled) {
            selectedCar = -1;
            carControlled = false;
            printf("Deseleccionado - Ningún carro cercano\n");
        }
    }
}

void mouseMotion(int x, int y) {
    int dx = x - mouseLastX; int dy = y - mouseLastY;
    mouseLastX = x; mouseLastY = y;
    
    // Si hay un carro controlado, moverlo con el mouse y desactivar control de cámara
    if (carControlled && selectedCar >= 0 && selectedCar < bumperCars.size()) {
        // Movimiento más suave y preciso
        bumperCars[selectedCar].x += dx * 0.08f;  // Mayor velocidad de movimiento
        bumperCars[selectedCar].z += dy * 0.08f;
        
        // Actualizar ángulo del carro para que mire hacia donde se mueve
        if (fabs(dx) > 0.1f || fabs(dy) > 0.1f) {
            bumperCars[selectedCar].angle = atan2(dy, dx) * 180.0f / M_PI;
        }
        
        // Mantener carro dentro de los límites con rebote suave
        float dist = sqrt(bumperCars[selectedCar].x * bumperCars[selectedCar].x + 
                         bumperCars[selectedCar].z * bumperCars[selectedCar].z);
        if (dist > BUMPER_ARENA_RADIUS - 2.0f) {
            // Rebote suave en lugar de pegarse
            bumperCars[selectedCar].x *= (BUMPER_ARENA_RADIUS - 2.5f) / dist;
            bumperCars[selectedCar].z *= (BUMPER_ARENA_RADIUS - 2.5f) / dist;
            spawnParticle(bumperCars[selectedCar].x, bumperCars[selectedCar].z, bumperCars[selectedCar].c);
        }
        
        // Forzar redibujado cuando se mueve el carro
        glutPostRedisplay();
        return;  // No procesar más controles de cámara
    }
    
    // Control normal de cámara solo cuando no hay carro controlado
    if (mouseLeftDown) {
        camYaw += dx * MOUSE_YAW_SPEED; camPit -= dy * MOUSE_PIT_SPEED;
        if (camPit > 89.0f) camPit = 89.0f; if (camPit < -89.0f) camPit = -89.0f;
        glutPostRedisplay();
    } else if (mouseRightDown) {
        float rad = camYaw * (float)(M_PI / 180.0f);
        camX -= cosf(rad) * dx * MOUSE_PAN_SPEED; camZ -= sinf(rad) * dx * MOUSE_PAN_SPEED;
        camY += dy * MOUSE_PAN_SPEED;
        glutPostRedisplay();
    }
}

void updateAnimations() {
    // Sillas voladoras - ciclos de velocidad
    if (animEnabled && animSillasVoladoras) {
        cycleTimer += 1.0f;

        switch (speedCycle) {
        case 0: // Acelerando
            if (rotationVel < 2.2f) rotationVel += 0.025f;
            if (currentSwing < targetSwing) currentSwing += 0.25f;
            if (rotationVel >= 2.2f) {
                speedCycle = 1;
                cycleTimer = 0.0f;
            }
            break;
        case 1: // Frenando
            if (rotationVel > 0.3f) rotationVel -= 0.035f;
            if (currentSwing > 10.0f) currentSwing -= 0.35f;
            if (rotationVel <= 0.3f) {
                speedCycle = 2;
                cycleTimer = 0.0f;
            }
            break;
        case 2: // Pausa
            if (cycleTimer > 40.0f) {
                speedCycle = 0;
                cycleTimer = 0.0f;
            }
            break;
        }
    } else {
        // Frenado normal cuando se pausa la animación
        if (rotationVel > 0.0f) rotationVel -= 0.02f; else rotationVel = 0.0f;
        if (currentSwing > 0.0f) currentSwing -= 0.15f; else currentSwing = 0.0f;
    }

    chairsAngle += rotationVel;
    if (chairsAngle >= 360.0f) chairsAngle -= 360.0f;

    // Movimiento de subida y bajada de las sillas (movimiento continuo)
    // Las sillas suben y bajan continuamente mientras giran
    float baseHeight = rotationVel * 0.8f; // Altura base según velocidad
    baseHeight = fmin(baseHeight, 2.5f); // Máximo base de 2.5 metros

    // Movimiento oscilatorio continuo (como las sillas voladoras reales)
    float oscillation = 1.2f * sin(chairsAngle * 0.12f); // Oscilación principal
    float smallOscillation = 0.3f * sin(chairsAngle * 0.25f); // Oscilación secundaria

    chairHeight = baseHeight + oscillation + smallOscillation;

    // Limitar altura total
    chairHeight = fmax(0.0f, fmin(chairHeight, 3.5f));

    if (!animEnabled) return;

    // Rueda de la fortuna
    if (animRuletaRusa) {
        wheelAngle += WHEEL_SPEED;
        if (wheelAngle >= 360.0f) wheelAngle -= 360.0f;
    }

    // Montaña rusa
    if (animMontanaRusa) {
        coasterT += 0.04f;
        if (coasterT > 2.0f * M_PI) coasterT -= 2.0f * M_PI;
    }

    // Gusanito
    if (animGusanito) {
        wormAngle += 0.8f;
        if (wormAngle >= 360.0f) wormAngle -= 360.0f;
    }

    // Carritos chocones
    if (animCarritosChocones) {
        updateBumperCars();
        updateParticles();
    }

    // Torre de caída - solo animar automáticamente si no está en modo manual
    if (animTorreCaida && !towerManualMode) {
        switch (towerPhase) {
        case 0: towerPlatformY += TOWER_RISE_SPEED;
            if (towerPlatformY >= TOWER_HEIGHT_TRAVEL) { towerPlatformY = TOWER_HEIGHT_TRAVEL; towerPhase = 1; towerTimer = 0.0f; }
            break;
        case 1: towerTimer += 1.0f;
            if (towerTimer >= TOWER_PAUSE_TIME) { towerPhase = 2; }
            break;
        case 2: towerPlatformY -= TOWER_FALL_SPEED;
            if (towerPlatformY <= 0.0f) { towerPlatformY = 0.0f; towerPhase = 0; towerTimer = 0.0f; }
            break;
        }
    }
}

void timerCallback(int value) {
    updateAnimations();
    glutPostRedisplay();
    glutTimerFunc(TIMER_MS, timerCallback, 0);
}

int main(int argc, char** argv) {
    glutInit(&argc, argv);
    glutInitDisplayMode(GLUT_DOUBLE | GLUT_RGB | GLUT_DEPTH);
    glutInitWindowSize(WINDOW_W, WINDOW_H);
    glutInitWindowPosition(80, 60);
    glutCreateWindow("Parque de Diversiones Modular — OpenGL");

    glClearColor(0.53f, 0.81f, 0.92f, 1.0f);
    glEnable(GL_DEPTH_TEST);
    glEnable(GL_NORMALIZE);
    glShadeModel(GL_SMOOTH);
    setupLights();

    gQuad = gluNewQuadric();
    gluQuadricNormals(gQuad, GLU_SMOOTH);

    glutDisplayFunc(display);
    glutReshapeFunc(reshape);
    glutKeyboardFunc(keyboard);
    glutSpecialFunc(specialKeys);
    glutMouseFunc(mouseButton);
    glutMotionFunc(mouseMotion);
    glutTimerFunc(TIMER_MS, timerCallback, 0);

    // Inicializar carritos chocones
    initBumperCars();

    // Inicializar SDL_mixer para audio
    initAudio();
    loadAudioFiles();

    // Crear y adjuntar menú contextual al clic derecho
    creacionMenu();

    glutMainLoop();
    return 0;
}
