usuario_admin=0
estudiante=0
matricular=0
profesores=0
alumnos=0
maestros=0
usuario=0
usuario_maestro=0
info_estudiante=0
info_maestro=0
consultar_estudiante=0
menu=0
consultar_maestro=0
eliminar_info_estudiante=0
eliminar_info_maestro=0

list=[]
materias=[]
profesor=[]
horas_materias=[]


def datos(estudiante):
    identificacion_estudiante=int(input("ingrese el numero de identificacion: "))
    nombre_estudiante=input("ingrese el nombre del estudiante: ")
    celular_estudiante=int(input("ingrese el contacto: "))
    jornada_estudiante=input("ingrese la jornada: ")
    list.append(identificacion_estudiante)
    list.append(nombre_estudiante)
    list.append(celular_estudiante)
    list.append(jornada_estudiante)
    print(" ")

def materia(matricular):
    print("se pueden minimo 2 y maximo 5")
    numero_materias=int(input("ingrese el numero de materias: "))
    while numero_materias < 2 or numero_materias > 5:
        numero_materias=int(input("numero invalido, ingrese el numero de materias: "))
    for i in range (0,numero_materias):
        nombre_materia=input("ingrese el nombre de la materia: ")
        intensidad_horaria=int(input("ingrese la duracion diaria: "))
        materias.append(nombre_materia)
        horas_materias.append(intensidad_horaria)
        print("----------------------------------------------------------------")
    total_horas=sum(horas_materias)

def datos_profesores(profesores):
    identificacion_profesor=int(input("ingrese la identificacion del maestro: "))
    nombre_profesor=input("ingrese el nombre del maestro: ")
    celular_profesor=int(input("ingrese el contacto del maestro: "))
    jornada_profesor=input("ingrese el tipo de jornada del maestro: ")
    materia_profesor=input("ingrese la materia que da: ")
    profesor.append(identificacion_profesor)
    profesor.append(nombre_profesor)
    profesor.append(celular_profesor)
    profesor.append(materia_profesor)
    profesor.append(jornada_profesor)
    print(" ")

def lista(alumnos):
    total_horas=sum(horas_materias)
    print("este es el listado del los estudiantes con sus respectivos datos")
    print(list)
    print("materias")
    print(materias)
    print("intensidad horaria")
    print(horas_materias)
    print("total horas")
    print(total_horas)
    print(" ")

def  lista_maestros(maestros):
    print("estos son los datos del maestro")
    print("datos del profesor: ",profesor)
    print(" ")


def editar(usuario):  
    while True:
        print(" ")
        print(" 1 identificacion")
        print(" 2 nombre")
        print(" 3 celular")
        print(" 4 jornada")
        print(" 5 materia")
        print(" 0  salir")
        print(" ")
        print("elija alguna opcion")
        editar1=int(input())
        if editar1 == 1:
            print("ingrese la nueva identificacion")
            nueva_identificacion_alumno=int(input())
            list[0] = nueva_identificacion_alumno
        elif editar1 == 2:
            print("ingrese el nuevo nombre")
            nuevo_nombre_alumno=input()
            list[1] = nuevo_nombre_alumno
        elif editar1 == 3:
            print("ingrese el nuevo celular")
            nuevo_celular_alumno=int(input())
            list[2] = nuevo_celular_alumno
        elif editar1 == 4:
            print("ingrese la nueva jornada ")
            nueva_jornada_alumno=input()
            list[3] = nueva_jornada_alumno
        elif editar1 == 5:
            print("ingrese la posicion de la materia que va a cambiar")
            print("-------------------------------------")
            print(materias)
            print("-------------------------------------")
            posicion_materia=int(input())
            print("ingrese la nueva materia")
            nueva_materia_alumno=input()
            materias[posicion_materia] = nueva_materia_alumno
            print("ingrese el numero de la hora que corresponde con la materia que cambio")
            print(horas_materias)
            eliminar_hora=int(input())
            horas_materias.remove(eliminar_hora)
            intensidad_horaria=int(input("ingrese la duracion diaria nueva: "))
            horas_materias.append(intensidad_horaria)
            total_horas=sum(horas_materias)
            print(" ")
        else:
            break

    

def editar_maestro(usuario_maestro):
    while True:
        print(" ")
        print(" 1 identificacion")
        print(" 2 nombre")
        print(" 3 celular")
        print(" 4 jornada")
        print(" 5 materia")
        print(" 0  salir")
        print(" ")
        print("elija alguna opcion")
        editar2=int(input())
        if editar2 == 1:
            print("ingrese la nueva identificacion")
            nueva_identificacion_maestro=int(input())
            profesor[0] = nueva_identificacion_maestro
        elif editar2 == 2:
            print("ingrese el nuevo nombre")
            nuevo_nombre_maestro=input()
            profesor[1] = nuevo_nombre_maestro
        elif editar2 == 3:
            print("ingrese el nuevo celular")
            nuevo_celular_maestro=int(input())
            profesor[2] = nuevo_celular_maestro
        elif editar2 == 4:
            print("ingrese la nueva jornada")
            nueva_jornada_maestro=input()
            profesor[4] = nueva_jornada_maestro
        elif editar2 == 5:
            print("ingrese la nueva materia")
            nueva_materia_maestro=input()
            profesor[3] = nueva_materia_maestro
        else:
            break

def eliminar(info_estudiante):
    while True:
        print(" ")
        print(" 1 identificacion")
        print(" 2 nombre")
        print(" 3 celular")
        print(" 4 jornada")
        print(" 5 materia")
        print(" 0 salir")
        print("------------------------")
        print(" ")
        opcion3=int(input("ingrese una opcion: "))
        print(" ")
        if opcion3 == 1:
            print("ingrese el numero de identificacion")
            eliminar_identificacion=int(input())
            list.remove(eliminar_identificacion)
        elif opcion3 == 2:
            print("ingrese el nombre para eliminar")
            eliminar_nombre=input()
            list.remove(eliminar_nombre)
        elif opcion3 == 3:
            print("ingrese el numero de celular para eliminar")
            eliminar_celular=int(input())
            list.remove(eliminar_celular)
        elif opcion3 == 4:
            print("ingrese el nombre de la jornada")
            eliminar_jornada=input()
            list.remove(eliminar_jornada)
        elif opcion3 == 5:
            print("ingrese el nombre de la materia que va a eliminar")
            print("-----------------------------------------")
            print(materias)
            print("-----------------------------------------")
            eliminar_materia=input()
            materias.remove(eliminar_materia)
            print("ingrese la hora de la materia que ingreso para eliminarlo")
            print("-----------------------------------------")
            print(horas_materias)
            print("-----------------------------------------")
            eliminar_hora=int(input())
            horas_materias.remove(eliminar_hora)
            total_horas=sum(horas_materias)
        else:
            break

def eliminar_maestro(info_maestro):
    while True:
        print(" ")
        print(" 1 identificacion")
        print(" 2 nombre")
        print(" 3 celular")
        print(" 4 jornada")
        print(" 5 materia")
        print(" 0 salir")
        print("------------------------")
        print(" ")
        opcion4=int(input("ingrese una opcion: "))
        print(" ")
        if opcion4 == 1:
            print("ingrese el numero de identificacion")
            eliminar_identificacion=int(input())
            profesor.remove(eliminar_identificacion)
        elif opcion4 == 2:
            print("ingrese el nombre para eliminar")
            eliminar_nombre=input()
            profesor.remove(eliminar_nombre)
        elif opcion4 == 3:
            print("ingrese el numero de celular para eliminar")
            eliminar_celular=int(input())
            profesor.remove(eliminar_celular)
        elif opcion4 == 4:
            print("ingrese el nombre de la jornada")
            eliminar_jornada=input()
            profesor.remove(eliminar_jornada)
        elif opcion4 == 5:
            print("ingrese el nombre de la materia")
            eliminar_materia=input()
            profesor.remove(eliminar_materia)
        else:
            break

def consultar(consultar_estudiante):
    total_horas=sum(horas_materias)
    nombre_estudiante2=input("ingrese el nombre del estudiante: ")
    nombre_estudiante3=input("verifique el nombre del estudiante: ")
    if nombre_estudiante2 == nombre_estudiante3:
        print("estos son los datos del estudiante")
        print(list)
        print("materias")
        print(materias)
        print("intensidad horaria")
        print(horas_materias)
        print("total horas")
        print(total_horas)
        print(" ")
    else:
        print("no se encontro el estudiante")

def consultar_maestro(consultar_maestro):
    nombre_maestro2=input("ingrese el nombre del maestro: ")
    nombre_maestro3=input("verifique el nombre del maestro: ")
    if nombre_maestro2 == nombre_maestro3:
        print("estos son los datos del maestro")
        print("datos del profesor: ",profesor)
        print(" ")

def eliminar_total(eliminar_info_estudiante):
    print(" ")
    desicion=int(input("Desea eliminar Toda la info del alumno? 1 para si / 0 para no: "))
    if desicion == 1:
        list.clear()
        materias.clear()
        horas_materias.clear()
        print("se han elimindo todos los datos")
        print(" ")
        print("-----------------------------------------------------")
    else:
        print("usted no elimino nada")
        print(" ")

def eliminar_total_maestro(eliminar_info_maestro):
    print(" ")
    desicionxd=int(input("Desea eliminar toda la info del maestro? 1 para si / 0 para no: "))
    if desicionxd == 1:
        profesor.clear()
        print("se han eliminado todos los datos")
        print(" ")
        print("------------------------------------------------------")
    else:
        print("usted no elimino nada")
        print(" ")
                    
def admin(usuario_admin):
    while True:
        print("------MENU ADMIN------")
        print(" ")
        print(" 1 ingresar datos estudiante")
        print(" 2 materias a matricular")
        print(" 3 datos profesores")
        print(" 4 lista de estudiantes")
        print(" 5 lista maestros")
        print(" 6 editar usuario estudiante")
        print(" 7 editar usurio profesor")
        print(" 8 eliminar info de estudiante")
        print(" 9 eliminar info de maestro")
        print(" 10 consultar algun estudiante")
        print(" 11 consultar algun maestro")
        print(" 12 eliminar info total estudiante")
        print(" 13 eliminar info total maestro")
        print(" 0 salir")
        print(" ")
        print("------------------------------------------")
        opcion2=int(input("ingrese una de las opciones : "))
        print(" ")

        if opcion2 == 1:
            datos(estudiante)
        elif opcion2 == 2:
            materia(matricular)
        elif opcion2 == 3:
            datos_profesores(profesores)
        elif opcion2 == 4:
            lista(alumnos)
        elif opcion2 == 5:
            lista_maestros(maestros)
        elif opcion2 == 6:
            editar(usuario)
        elif opcion2 == 7:
            editar_maestro(usuario_maestro)
        elif opcion2 == 8:
            eliminar(info_estudiante)
        elif opcion2 == 9:
            eliminar_maestro(info_maestro)
        elif opcion2 == 10:
            consultar(consultar_estudiante)
        elif opcion2 == 11:
            consultar_maestro(consultar_maestro)
        elif opcion2 == 12:
            eliminar_total(eliminar_info_estudiante)
        elif opcion2 == 13:
            eliminar_total_maestro(eliminar_info_maestro)
        else:
            break
            
        
def auxiliar(menu):
    while True:
        print("------MENU AUXILIAR------")
        print("1 consultar estudiante")
        print("2 modificar info estudiante")
        print("0 salir")
        print(" ")
        print("-------------------------------------")
        print("ingrese una de las opciones")
        opciones1=int(input())

        if opciones1 == 1:
            consultar(consultar_estudiante)
        elif opciones1 == 2:
            editar(usuario)
        else:
            break
            
            
    

while True:
        print("-------MENU DE OPCIONES-------")
        print(" ")
        print(" 1 usuario administrador")
        print(" 2 usuario auxiliar")
        print(" 0 salir")
        print(" ")
        print("-----------------------------------------------")
        print(" ")
        opciones=int(input("ingrese una de esas opciones: "))
        print(" ")
        print("ingrese la contraseña para entrar al programa")
        print(" ")
        contraseña=int(input())
        if contraseña == 777:
            if opciones == 1:
                admin(usuario_admin)
            elif opciones == 2:
                auxiliar(menu)
            else:
                print("gracias por usar el programa xd")
                break
        else:
            print("contraseña incorrecta")
            print(" ")
        