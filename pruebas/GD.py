import pygame
import random

# Inicializamos pygame
pygame.init()

# Definimos algunas constantes
ANCHO = 800
ALTO = 600
FPS = 60

# Colores
NEGRO = (0, 0, 0)
BLANCO = (255, 255, 255)
AZUL = (0, 0, 255)
ROJO = (255, 0, 0)

# Configuración de la pantalla
pantalla = pygame.display.set_mode((ANCHO, ALTO))
pygame.display.set_caption("Geometry Dash")

# Reloj para controlar la velocidad de los fotogramas
reloj = pygame.time.Clock()

# Clase para el jugador (cubo)
class Jugador(pygame.sprite.Sprite):
    def __init__(self):
        super().__init__()
        self.image = pygame.Surface((50, 50))
        self.image.fill(AZUL)
        self.rect = self.image.get_rect()
        self.rect.x = 100
        self.rect.y = ALTO - 150
        self.velocidad_y = 0
        self.en_suelo = False

    def update(self):
        # Gravedad
        if not self.en_suelo:
            self.velocidad_y += 1  # Aceleración hacia abajo
        self.rect.y += self.velocidad_y

        # Verificar si el jugador tocó el suelo
        if self.rect.y >= ALTO - 150:
            self.rect.y = ALTO - 150
            self.en_suelo = True
            self.velocidad_y = 0

    def saltar(self):
        if self.en_suelo:
            self.velocidad_y = -15
            self.en_suelo = False

# Clase para los obstáculos
class Obstacle(pygame.sprite.Sprite):
    def __init__(self):
        super().__init__()
        self.image = pygame.Surface((50, 50))
        self.image.fill(ROJO)
        self.rect = self.image.get_rect()
        self.rect.x = ANCHO
        self.rect.y = ALTO - 150  # Ajusta la posición de los obstáculos al suelo

    def update(self):
        self.rect.x -= 5  # Mover el obstáculo hacia la izquierda
        if self.rect.x < -50:  # Si el obstáculo sale de la pantalla, se elimina
            self.rect.x = ANCHO
            self.rect.y = ALTO - 150  # Resetear la posición
            self.image.fill(random.choice([ROJO, (255, 255, 0)]))  # Cambiar color aleatorio

# Función principal del juego
def juego():
    # Crear los grupos de sprites
    todos_los_sprites = pygame.sprite.Group()
    obstáculos = pygame.sprite.Group()

    # Crear el jugador y añadirlo a los grupos
    jugador = Jugador()
    todos_los_sprites.add(jugador)

    # Crear obstáculos
    for i in range(5):
        obstáculo = Obstacle()
        todos_los_sprites.add(obstáculo)
        obstáculos.add(obstáculo)

    # Bucle principal del juego
    corriendo = True
    while corriendo:
        for evento in pygame.event.get():
            if evento.type == pygame.QUIT:
                corriendo = False
            if evento.type == pygame.KEYDOWN:
                if evento.key == pygame.K_SPACE:  # Al presionar la barra espaciadora, el jugador salta
                    jugador.saltar()

        # Actualizar todos los sprites
        todos_los_sprites.update()

        # Verificar si el jugador choca con un obstáculo
        if pygame.sprite.spritecollide(jugador, obstáculos, False):
            print("¡Has perdido!")
            corriendo = False

        # Rellenar la pantalla con negro
        pantalla.fill(NEGRO)

        # Dibujar todos los sprites
        todos_los_sprites.draw(pantalla)

        # Actualizar la pantalla
        pygame.display.flip()

        # Controlar la velocidad de los fotogramas
        reloj.tick(FPS)

    # Finalizar pygame
    pygame.quit()

# Llamar a la función del juego
if __name__ == "__main__":
    juego()
 