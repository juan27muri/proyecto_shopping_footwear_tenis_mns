import tkinter as tk
from tkinter import messagebox

def suma():
    try:
        n1 = int(entry1.get())
        n2 = int(entry2.get())
        resultado = n1 + n2
        messagebox.showinfo("Resultado", f"La suma es: {resultado}")
    except ValueError:
        messagebox.showerror("Error", "Por favor ingrese números válidos.")

def resta():
    try:
        n1 = int(entry1.get())
        n2 = int(entry2.get())
        resultado = n1 - n2
        messagebox.showinfo("Resultado", f"La resta es: {resultado}")
    except ValueError:
        messagebox.showerror("Error", "Por favor ingrese números válidos.")

def multiplicacion():
    try:
        n1 = int(entry1.get())
        n2 = int(entry2.get())
        resultado = n1 * n2
        messagebox.showinfo("Resultado", f"La multiplicación es: {resultado}")
    except ValueError:
        messagebox.showerror("Error", "Por favor ingrese números válidos.")

def division():
    try:
        n1 = int(entry1.get())
        n2 = int(entry2.get())
        if n2 == 0:
            messagebox.showerror("Error", "No se puede dividir por cero.")
        else:
            resultado = n1 / n2
            messagebox.showinfo("Resultado", f"La división es: {resultado}")
    except ValueError:
        messagebox.showerror("Error", "Por favor ingrese números válidos.")

def salir():
    ventana.destroy()

ventana = tk.Tk()
ventana.title("Calculadora Básica")
ventana.geometry("300x400")

label1 = tk.Label(ventana, text="Ingrese el primer número:")
label1.pack(pady=5)
entry1 = tk.Entry(ventana)
entry1.pack(pady=5)

label2 = tk.Label(ventana, text="Ingrese el segundo número:")
label2.pack(pady=5)
entry2 = tk.Entry(ventana)
entry2.pack(pady=5)

btn_suma = tk.Button(ventana, text="Sumar", command=suma)
btn_suma.pack(pady=5)

btn_resta = tk.Button(ventana, text="Restar", command=resta)
btn_resta.pack(pady=5)

btn_multiplicacion = tk.Button(ventana, text="Multiplicar", command=multiplicacion)
btn_multiplicacion.pack(pady=5)

btn_division = tk.Button(ventana, text="Dividir", command=division)
btn_division.pack(pady=5)

btn_salir = tk.Button(ventana, text="Salir", command=salir)
btn_salir.pack(pady=20)

ventana.mainloop()
