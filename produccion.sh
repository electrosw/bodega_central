#!/bin/bash

# SCRIPT PARA SINCRONIZAR LOS CAMBIOS CON EL SERVIDOR DE PRODUCCION

# IMPORTANTE EJECUTAR SCRIPT COMO ROOT O CON SUDO

servidor[0]="192.168.11.215"
servidor[1]="0"
servidor[2]="0"
servidor[3]="0"
servidor[4]="0"
servidor[5]="0"
servidor[6]="0"
servidor[7]="192.168.23.101"
servidor[8]="0"
servidor[9]="0"
servidor[10]="0"
servidor[11]="0"
servidor[12]="0"
servidor[13]="0"
servidor[14]="0"
servidor[15]="0"
servidor[16]="0"
servidor[17]="0"
servidor[18]="0"
servidor[20]="192.168.11.215"

clear
echo
echo "=============================="
echo "|| SCRIPT DE SINCRONIZACION ||"
echo "=============================="
echo
echo
echo "OPCIONES:"
echo "[ 1 - 18 ] (SERVIDORES LOCALES)"
echo "[ 0      ] TODOS LOS SERVIDORES"
echo
echo "INGRESA EL NUMERO DEL SERVIDOR: "
#read num
num=7

sincroniza (){
    cd /srv/repos/bodega_central/
    ssh root@"${servidor[$num]}" 'mkdir /srv/www/inverluz/mod/bodega_central > /dev/null 2>&1 && rm -rf /srv/www/inverluz/mod/bodega_central/public > /dev/null 2>&1'
    rsync -av --exclude-from='excluye_rsync.txt' . "${servidor[$num]}":/srv/www/inverluz/mod/bodega_central/.
}

if [[ $num -gt 20 ]]; then
	echo "SERVIDOR NO EXISTE."
else
    if [[ $num -eq 0 ]]; then
        echo "SINCRONIZANDO A TODOS LOS SERVIDORES LOCALES."
    else
        if [[ "${servidor[$num]}" = "0" ]]; then
            echo "SERVIDOR NO EXISTE."
        else
            echo "SINCRONIZANDO AL SERVIDOR ${servidor[$num]}" 
            sincroniza
        fi
    fi
fi

