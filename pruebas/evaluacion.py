
def matriculas_estudiantes(estudiantes):
    cantidad_estudiantes = int(input("Ingrese la cantidad de estudiantes: "))
    for i in range(1,cantidad_estudiantes + 1):
        identificacion_estudiante = input(f"Ingrese la identificacion del estudiante {i}: ")
        nombre_estudiante = input("Ingrese el nombre del estudiante: ")
        apellido_estudiante = input("ingrese el apellido del estudiante: ")
        direccion_estudiante = input("ingrese la direccion del estudiante: ")
        telefono_estudiante = input("ingrese el numero de telefono del estudiante: ")
        estudiante = {"identificacion": identificacion_estudiante,"nombre":nombre_estudiante, "apellido": apellido_estudiante, "direccion": direccion_estudiante, "telefono": telefono_estudiante }
        estudiantes[identificacion_estudiante] = estudiante
    print("---- LISTADO DE ESTUDIANTES MATRICULADO ----")
    for identificacion_estudiante, estudiante in estudiantes.items():
        print("------------------")
        print("IDENTEFICACION: " + estudiante["identificacion"])
        print("NOMBRE: " + estudiante["nombre"])
        print("APELLIDO: " + estudiante["apellido"])
        print("DIRECCION: " + estudiante["direccion"])
        print("TELEFONO: " + estudiante["telefono"])
        print("------------------")

def boletines_estudiantes(boletines):
    cantidad_estudiantes = int(input("Ingrese la cantidad de estudiantes: "))
    for i in range(1,cantidad_estudiantes + 1):
        identificacion_estudiante = input(f"Ingrese la identificacion del estudiante {i}: ")
        nombre_estudiante = input("Ingrese el nombre del estudiante: ")
        apellido_estudiante = input("ingrese el apellido del estudiante: ")
        print("ingrese las 4 notas del estudiante, las notas no deben ser mayores a 5 y menos a 0")
        nota1 = float(input(f"Ingrese la nota 1 : "))
        while nota1 > 5 or nota1 < 0:
            print("las notas no deben ser mayores a 5 y menos a 0")
            nota1 = float(input("Vuelva a ingresar la nota 1 : "))
        nota2 = float(input(f"Ingrese la nota 2 : "))
        while nota2 > 5 or nota2 < 0:
            print("las notas no deben ser mayores a 5 y menos a 0")
            nota2 = float(input("Vuelva a ingresar la nota 2 : "))
        nota3 = float(input(f"Ingrese la nota 3 : "))
        while nota3 > 5 or nota3 < 0:
            print("las notas no deben ser mayores a 5 y menos a 0")
            nota3 = float(input("Vuelva a ingresar la nota 3 : "))
        nota4 = float(input(f"Ingrese la nota 4 : "))
        while nota4 > 5 or nota4 < 0:
            print("las notas no deben ser mayores a 5 y menos a 0")
            nota4 = float(input("Vuelva a ingresar la nota 4 : "))
        notas = nota1,nota2,nota3,nota4
        promedio = (nota1+nota2+nota3+nota4)/4
        boletin = {"identificacion": identificacion_estudiante,"nombre": nombre_estudiante, "apellido": apellido_estudiante, "nota": notas, "promedio": promedio}
        boletines[identificacion_estudiante] = boletin
        for identificacion_estudiante, boletin in boletines.items():
            print("-----------------------")
            print("-----BOLETINES DE LOS ESTUDIANTES-----")
            print("IDENTEFICACION: " + boletin["identificacion"])
            print("NOMBRE: " + boletin["nombre"])
            print("APELLIDO: " + boletin["apellido"])
            print("NOTAS: "  ,boletin["nota"])
            print("PROMEDIO: "  ,boletin["promedio"])
            if promedio > 4:
                print("APROBADO")
            else:
                print("REPROBADO")

            if promedio >= 4.5:
                print("RENDIMIENTO EXCELENTE")
            elif promedio < 4.5 and promedio >= 4:
                print("RENDIMIENTO BUENO")
            else:
                print("RENDIMIENTO MALO")

def modulo_nomina():
    print("ingrese el numero de profesores: ")
    n=int(input())
    for i in range(0,n):
        identificacion_profesor=int(input("ingrese el numero de identificacion: "))
        nombre_profesor=input("ingrese el nombre: ")
        apellido_profesor=input("ingrese el apellido: ")
        sueldo_profesor=int(input("ingrese el sueldo: "))
        if sueldo_profesor > 2320000:
            print("el docente tiene auxilio de transporte")
        else:
            print("el docente no tiene auxilio de trasnporte")
        print("-------------------------------------------------------------")
        dias_trabajados=int(input("ingrese los dias trabajados del docente, no pueden ser superiores a 30 o menores a 0: "))
        print("-------------------------------------------------------------")
        print(" ")
        print("se tendra que descontar el 4% del sueldo")
        print("se tendra que descontar el 4% de la salud")
        descuento_sueldo=sueldo_profesor*4/100
        descuento_salud=sueldo_profesor*4/100
        sueldo_diario=sueldo_profesor/dias_trabajados
        print(" ")
        print("-------------------------------------------------------")
        print("el descuento del 4% del sueldo es de: $ ",descuento_sueldo)
        print("el descuento del 4% de la salud es de: $ ",descuento_salud)
        print("el sueldo diario es de: $ ",sueldo_diario)
        print("-------------------------------------------------------")
        print(" ")
        libranza=int(input("el docente tiene libranza 1 para si/0 para no?: "))
        if  libranza == 1:
            print("de cuanto es el valor del prestamo de la libranza?")
            valor_libranza=int(input())
        else:
            print("el docente no tiene libranza")
            valor_libranza=0
            print("-----------------------------------------------")
            print(" ")
        auxilio_trasnporte=140606
        valor_total=descuento_sueldo+descuento_salud+auxilio_trasnporte
        if sueldo_profesor > 2320000:
            print("------------------DATOS DEL DOCENTE------------------")
            print(" ")
            print("el docente: ",nombre_profesor)
            print("con apellido ",apellido_profesor)
            print("con identificacion: ",identificacion_profesor)
            print("con ",dias_trabajados," dias trabajados")
            print("con una libranza de: $",valor_libranza)
            print(" ")
            print("-----------------DATOS DE IMPUESTO-----------------")
            print(" ")
            print("valor del 4% del descuento de salud: $",descuento_salud)
            print("valor del 4% del descuento de pension: $",descuento_sueldo)
            print("con auxilio de trasnporte de: $",auxilio_trasnporte)
            print("con un total de: $",valor_total)
            print(" ")
            valor_final=valor_libranza-valor_total
            print("------------------VALOR FINAL------------------")
            print(" ")
            print("el valor final con el descuento de la libranza es de: $",valor_final)
            print(" ")
        else:
            print("------------------DATOS DEL DOCENTE------------------")
            print(" ")
            print("el docente: ",nombre_profesor)
            print("con apellido ",apellido_profesor)
            print("con identificacion: ",identificacion_profesor)
            print("con ",dias_trabajados," dias trabajados")
            print("con una libranza de: $",valor_libranza)
            print(" ")
            valor_total2=descuento_salud+descuento_sueldo
            print("-----------------DATOS DE IMPUESTO-----------------")
            print(" ")
            print("valor del 4% del descuento de salud: $",descuento_salud)
            print("valor del 4% del descuento de pension: $",descuento_sueldo)
            print("no aplica auxilio de transporte")
            print("con un total de: $",valor_total2)
            print(" ")
            valor_final=valor_total2-valor_libranza
            print("------------------VALOR FINAL------------------")
            print(" ")
            print("el valor final sin la libranza es: $",valor_final)


boletines = {}
estudiantes = {}


while True:
    print("-----I.E EL RUBI-----")
    print(" ")
    print("-----MENU DE OPCIONES-----")
    print("1. Modulo matriculas")
    print("2. Modulo boletines")
    print("3. Modulo nomina")
    print("4. Modulo escrutinio")
    print("0. Salir")
    print(" ")
    opcion = int(input("Ingrese un Modulo correspondiente: "))
    if opcion == 1:
        matriculas_estudiantes(estudiantes)

    elif opcion == 2:
        boletines_estudiantes(boletines)

    elif opcion == 3:
        modulo_nomina()

    elif opcion == 4:
        print(" ")
    elif opcion == 0:
        print("Gracias por usar la aplicacion :)")
        break
    else:
        print("Opcion Invalida")

