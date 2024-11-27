import pygame
import sys

# Inicializar Pygame
pygame.init()

# Configuración de pantalla
ANCHO = 800
ALTO = 600
screen = pygame.display.set_mode((ANCHO, ALTO))
pygame.display.set_caption("Parkour Tipo Minecraft")

# Colores
BLANCO = (255, 255, 255)
NEGRO = (0, 0, 0)
VERDE = (0, 255, 0)
AZUL = (0, 0, 255)

# Reloj para controlar FPS
clock  = pygame.time.Clock()
FPS = 15

# Clase para el jugador
class Jugador(pygame.sprite.Sprite):
    def __init__(self):
        super().__init__()
        self.image = pygame.Surface((40, 40))
        self.image.fill(AZUL)
        self.rect = self.image.get_rect()
        self.rect.center = (100, ALTO - 100)
        self.vel_y = 0
        self.en_suelo = False

    def update(self):
        # Gravedad
        self.vel_y += 1
        if self.vel_y > 10:
            self.vel_y = 10

        # Movimiento vertical
        self.rect.y += self.vel_y

        # Colisiones con plataformas
        self.en_suelo = False
        for plataforma in plataformas:
            if self.rect.colliderect(plataforma.rect) and self.vel_y >= 0:
                self.rect.bottom = plataforma.rect.top
                self.vel_y = 0
                self.en_suelo = True

        # Movimiento horizontal
        keys = pygame.key.get_pressed()
        if keys[pygame.K_LEFT]:
            self.rect.x -= 5
        if keys[pygame.K_RIGHT]:
            self.rect.x += 5

    def saltar(self):
        if self.en_suelo:
            self.vel_y = -15

# Clase para plataformas
class Plataforma(pygame.sprite.Sprite):
    def __init__(self, x, y, ancho, alto):
        super().__init__()
        self.image = pygame.Surface((ancho, alto))
        self.image.fill(VERDE)
        self.rect = self.image.get_rect()
        self.rect.topleft = (x, y)

# Crear grupos de sprites
jugador = Jugador()
plataformas = pygame.sprite.Group()
all_sprites = pygame.sprite.Group(jugador)

# Crear plataformas
niveles = [
    (0, ALTO - 20, ANCHO, 20),
    (200, 500, 100, 20),
    (400, 400, 100, 20),
    (600, 300, 100, 20),
    (700, 200, 100, 20)
]

for nivel in niveles:
    plataforma = Plataforma(*nivel)
    plataformas.add(plataforma)
    all_sprites.add(plataforma)

# Bucle principal
ejecutando = True
while ejecutando:
    for evento in pygame.event.get():
        if evento.type == pygame.QUIT:
            ejecutando = False
        if evento.type == pygame.KEYDOWN:
            if evento.key == pygame.K_SPACE:
                jugador.saltar()

    # Actualizar
    all_sprites.update()

    # Dibujar
    screen.fill(BLANCO)
    all_sprites.draw(screen)
    
    # Mostrar cambios
    pygame.display.flip()

    # Controlar FPS
    clock.tick(FPS)

pygame.quit()
sys.exit()
