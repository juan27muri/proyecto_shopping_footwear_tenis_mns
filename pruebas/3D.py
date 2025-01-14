from ursina import *
from ursina.prefabs.first_person_controller import FirstPersonController
import pygetwindow as gw
from pynput import mouse  # Para detectar el arrastre del mouse

# Inicializar el motor
app = Ursina()

# Crear el jugador (cámara con controles)
player = FirstPersonController()

# Crear el suelo
ground = Entity(
    model='plane',
    texture='grass',
    collider='box',
    scale=(100, 1, 100)
)

# Crear bloques como plataformas
for z in range(10):
    for x in range(10):
        block = Entity(
            model='cube',
            color=color.green if (x + z) % 2 == 0 else color.brown,
            texture='white_cube',
            position=(x, 0.5, z),
            collider='box'
        )

# Variables para el control del arrastre
is_dragging = False
start_pos = None

def on_mouse_drag(x, y, button, pressed):
    global is_dragging, start_pos
    if button == mouse.Button.left:
        if pressed:
            is_dragging = True
            start_pos = (x, y)
        else:
            is_dragging = False

def on_mouse_move(x, y):
    global start_pos
    if is_dragging:
        win = gw.getWindowsWithTitle('Ursina Engine')[0]
        dx = x - start_pos[0]
        dy = y - start_pos[1]
        win.move(win.left + dx, win.top + dy)
        start_pos = (x, y)

# Listener para el mouse
listener = mouse.Listener(on_move=on_mouse_move, on_click=on_mouse_drag)
listener.start()

def update():
    speed = 5 * time.dt
    if held_keys['w']:
        player.position += (0, 0, speed)
    if held_keys['s']:
        player.position -= (0, 0, speed)
    if held_keys['a']:
        player.position -= (speed, 0, 0)
    if held_keys['d']:
        player.position += (speed, 0, 0)
    if held_keys['space'] and player.grounded:
        player.jump()
    if held_keys['shift']:
        speed *= 2 
        

def generate_parkour_blocks():
    for i in range(10):
        block = Entity(
            model='cube',
            color=color.random_color(),
            texture='white_cube',
            position=(i, i + 0.5, i * 2),
            collider='box'
        )

generate_parkour_blocks()


# Ejecutar el bucle principal
app.run()
