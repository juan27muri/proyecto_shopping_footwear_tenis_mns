import pygame
import sys

# Inicializar Pygame
pygame.init()

# Configuración de pantalla
ANCHO = 800
ALTO = 700
screen = pygame.display.set_mode((ANCHO, ALTO))
pygame.display.set_caption("Parkour Tipo Minecraft")

# Colores
BLANCO = (255, 255, 255)
NEGRO = (0, 0, 0)
VERDE = (0, 255, 0)
AZUL = (0, 0, 255)
ROJO = (255, 0, 0)

# Reloj para controlar FPS
clock = pygame.time.Clock()
FPS = 60

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

# Clase para enemigos
class Enemigo(pygame.sprite.Sprite):
    def __init__(self, x, y, ancho, alto, limite_izq, limite_der):
        super().__init__()
        self.image = pygame.Surface((ancho, alto))
        self.image.fill(ROJO)
        self.rect = self.image.get_rect()
        self.rect.topleft = (x, y)
        self.vel_x = 3
        self.limite_izq = limite_izq
        self.limite_der = limite_der

    def update(self):
        self.rect.x += self.vel_x
        if self.rect.left <= self.limite_izq or self.rect.right >= self.limite_der:
            self.vel_x *= -1

# Crear grupos de sprites
jugador = Jugador()
plataformas = pygame.sprite.Group()
enemigos = pygame.sprite.Group()
all_sprites = pygame.sprite.Group(jugador)

# Crear plataformas y enemigos
niveles = [
    (0, ALTO - 20, ANCHO, 20),
    (200, 500, 100, 20),
    (400, 400, 100, 20),
    (600, 300, 100, 20),
    (700, 200, 100, 20),
    (300, 100, 100, 20),
    (500, 50, 150, 20)
]

# Crear plataformas
for nivel in niveles:
    plataforma = Plataforma(*nivel)
    plataformas.add(plataforma)
    all_sprites.add(plataforma)

# Crear enemigos
enemigos_data = [
    (220, 480, 40, 40, 200, 300),
    (420, 380, 40, 40, 400, 500),
    (620, 280, 40, 40, 600, 700),
    (320, 80, 40, 40, 300, 400),
    (520, 30, 40, 40, 500, 650)
]

for enemigo_data in enemigos_data:
    enemigo = Enemigo(*enemigo_data)
    enemigos.add(enemigo)
    all_sprites.add(enemigo)

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

    # Colisiones con enemigos
    if pygame.sprite.spritecollideany(jugador, enemigos):
        print("¡Has perdido!")
        ejecutando = False

    # Dibujar
    screen.fill(BLANCO)
    all_sprites.draw(screen)

    # Mostrar cambios
    pygame.display.flip()

    # Controlar FPS
    clock.tick(FPS)

pygame.quit()
sys.exit()
