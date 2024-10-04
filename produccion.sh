#!/bin/bash

# SCRIPT PARA SINCRONIZAR LOS CAMBIOS CON EL SERVIDOR DE PRODUCCION

# IMPORTANTE EJECUTAR SCRIPT COMO ROOT O CON SUDO

servidor[0]="0"
servidor[1]="192.168.21.98"
servidor[2]="192.168.29.98"
servidor[3]="192.168.19.98"
servidor[4]="192.168.13.98"
servidor[5]="192.168.25.98"
servidor[6]="192.168.49.98"
servidor[7]="0"
servidor[8]="192.168.17.98"
servidor[9]="192.168.23.98"
servidor[10]="0"
servidor[11]="192.168.39.98"
servidor[12]="192.168.45.98"
servidor[13]="192.168.47.98"
servidor[14]="192.168.31.98"
servidor[15]="192.168.43.98"
servidor[16]="192.168.45.198"
servidor[17]="0"
servidor[18]="192.168.51.98"

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
read num

sincroniza (){
    cd /srv/repos/rhumanos/
    ssh root@"${servidor[$num]}" 'mkdir /srv/www/rhumanos > /dev/null 2>&1 && rm -rf /srv/www/rhumanos/public > /dev/null 2>&1'
    rsync -av --exclude-from='excluye_rsync.txt' . "${servidor[$num]}":/srv/www/rhumanos/.

}

if [[ $num -gt 18 ]]; then
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

#cd /srv/repos/rhumanos/
#ssh root@192.168.13.98 'rm -rf /srv/www/rhumanos/public'
#rsync -av --exclude-from='excluye_rsync.txt' . root@192.168.13.98:/srv/www/rhumanos/.
