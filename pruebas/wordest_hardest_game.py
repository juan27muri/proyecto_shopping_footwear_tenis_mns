import pygame
import sys
import random

# Inicializar pygame
pygame.init()

# Configuraciones de pantalla
WIDTH, HEIGHT = 800, 600
screen = pygame.display.set_mode((WIDTH, HEIGHT))
pygame.display.set_caption("World's Simplified Hardest Game")

# Colores
WHITE = (255, 255, 255)
RED = (255, 0, 0)
BLUE = (0, 0, 255)
GREEN = (0, 255, 0)
BLACK = (0, 0, 0)

# Reloj para controlar los FPS
clock = pygame.time.Clock()
FPS = 60

# Clase para el jugador
class Player(pygame.sprite.Sprite):
    def __init__(self):
        super().__init__()
        self.image = pygame.Surface((30, 30))
        self.image.fill(GREEN)
        self.rect = self.image.get_rect()
        self.rect.center = (50, HEIGHT // 2)
        self.speed = 5

    def update(self, keys):
        if keys[pygame.K_UP] and self.rect.top > 0:
            self.rect.y -= self.speed
        if keys[pygame.K_DOWN] and self.rect.bottom < HEIGHT:
            self.rect.y += self.speed
        if keys[pygame.K_LEFT] and self.rect.left > 0:
            self.rect.x -= self.speed
        if keys[pygame.K_RIGHT] and self.rect.right < WIDTH:
            self.rect.x += self.speed

# Clase para los enemigos
class Enemy(pygame.sprite.Sprite):
    def __init__(self, x, y, speed):
        super().__init__()
        self.image = pygame.Surface((20, 20))
        self.image.fill(RED)
        self.rect = self.image.get_rect()
        self.rect.center = (x, y)
        self.speed = speed

    def update(self):
        self.rect.y += self.speed
        if self.rect.top > HEIGHT or self.rect.bottom < 0:
            self.speed *= -1

# Clase para los objetivos
class Goal(pygame.sprite.Sprite):
    def __init__(self, x, y):
        super().__init__()
        self.image = pygame.Surface((50, 50))
        self.image.fill(BLUE)
        self.rect = self.image.get_rect()
        self.rect.center = (x, y)

# Instanciar jugador y otros elementos
player = Player()
all_sprites = pygame.sprite.Group()
enemies = pygame.sprite.Group()
goals = pygame.sprite.Group()

all_sprites.add(player)

# Añadir enemigos
for _ in range(5):
    x = random.randint(200, WIDTH - 200)
    y = random.randint(50, HEIGHT - 50)
    speed = random.choice([-3, 3])
    enemy = Enemy(x, y, speed)
    all_sprites.add(enemy)
    enemies.add(enemy)

# Añadir meta
goal = Goal(WIDTH - 50, HEIGHT // 2)
all_sprites.add(goal)
goals.add(goal)

# Bucle principal
def main():
    running = True
    while running:
        screen.fill(WHITE)

        for event in pygame.event.get():
            if event.type == pygame.QUIT:
                running = False

        # Movimiento del jugador
        keys = pygame.key.get_pressed()
        player.update(keys)

        # Actualizar enemigos
        enemies.update()

        # Verificar colisiones
        if pygame.sprite.spritecollideany(player, enemies):
            print("¡Has perdido!")
            running = False

        if pygame.sprite.spritecollideany(player, goals):
            print("¡Has ganado!")
            running = False

        # Dibujar todo
        all_sprites.draw(screen)

        pygame.display.flip()
        clock.tick(FPS)

    pygame.quit()
    sys.exit()

if __name__ == "__main__":
    main()
