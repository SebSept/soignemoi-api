#!/bin/env bash

## Tests sqlmap

# Liste des rôles valides
declare -a valid_roles=("visitor" "patient" "doctor" "secretary" "admin")

#  paramètre de lancement du script est le token d'identification à l'api
role=$1
token=$2

# Fonction pour afficher l'aide
display_help() {
    echo ""
    echo "Usage: ./tests.bash [role] [token]"
    echo "role: Le rôle de l'utilisateur. Doit être l'un des suivants : ${valid_roles[*]}"
    echo "token: Le token d'identification à l'api"
}

# aide --help | -h
for arg in "$@"
do
    if [ "$arg" == "--help" ] || [ "$arg" == "-h" ]
    then
        display_help
        exit 0
    fi
done



# Vérifier si le rôle est valide
if [[ ! " ${valid_roles[@]} " =~ " ${role} " ]]; then
    echo "Erreur : Le rôle '${role}' n'est pas valide. Les rôles valides sont : ${valid_roles[*]}"
    display_help
    exit 2
fi

echo "Role choisi :${role}"

if [ -z "$token" ]
then
  echo "Le paramètre token n'a pas été passé."
  display_help
  exit 3
fi

echo "Token utilisé : ${token}"

echo "Lancement des commandes ..."

# Exécutez des commandes spécifiques en fonction du rôle
if [ "$role" == "visitor" ]
then
  echo "Exécution des commandes pour le rôle visitor"
  # Insérez ici les commandes pour le rôle patient
elif [ "$role" == "patient" ]
then
  echo "Exécution des commandes pour le rôle patient"
  # Insérez ici les commandes pour le rôle patient
elif [ "$role" == "doctor" ]
then
  echo "Exécution des commandes pour le rôle doctor"
  # Insérez ici les commandes pour le rôle doctor
else
  echo "Exécution des commandes pour les autres rôles"
  # I
fi

exit 7;

./sqlmap.py -u http://localhost:32772/api/patients --method POST --data '{ "firstname": "string", "lastname": "string", "address1": "string", "address2": "string", "password": "string", "userCreationEmail": "user@example.com", "userCreationPassword": "password-verx-y7-strang" }' --headers='Accept: application/ld+json\nContent-Type: application/json' -p "userCreationPassword" --tamper=tamper/random_email.py --level=1 --risk=1 --ignore-code 422 --dbms PostgreSQL --batch

Route app_get_token POST "/token"
Tests sans authentification requise

  _api_/doctors{._format}_get_collection                          GET      ANY      ANY    /api/doctors.{_format}
  _api_/doctors/{id}{._format}_get                                GET      ANY      ANY    /api/doctors/{id}.{_format}
  _api_/doctors{._format}_post                                    POST     ANY      ANY    /api/doctors.{_format}
  _api_/doctors/{id}{._format}_patch                              PATCH    ANY      ANY    /api/doctors/{id}.{_format}
  _api_/hospital_stays{._format}_get_collection                   GET      ANY      ANY    /api/hospital_stays.{_format}
  _api_/hospital_stays/today_entries_get_collection               GET      ANY      ANY    /api/hospital_stays/today_entries
  _api_/hospital_stays/today_exits_get_collection                 GET      ANY      ANY    /api/hospital_stays/today_exits
  _api_/doctors/{doctor_id}/hospital_stays/today_get_collection   GET      ANY      ANY    /api/doctors/{doctor_id}/hospital_stays/today
  _api_/patients/hospital_stays_get_collection                    GET      ANY      ANY    /api/patients/hospital_stays
  _api_/hospital_stays/{id}{._format}_get                         GET      ANY      ANY    /api/hospital_stays/{id}.{_format}
  _api_/hospital_stays{._format}_post                             POST     ANY      ANY    /api/hospital_stays.{_format}
  _api_/hospital_stays/{id}{._format}_patch                       PATCH    ANY      ANY    /api/hospital_stays/{id}.{_format}
  _api_/medical_opinions{._format}_get_collection                 GET      ANY      ANY    /api/medical_opinions.{_format}
  _api_/medical_opinions/{id}{._format}_get                       GET      ANY      ANY    /api/medical_opinions/{id}.{_format}
  _api_/medical_opinions{._format}_post                           POST     ANY      ANY    /api/medical_opinions.{_format}
  _api_/medical_opinions/{id}{._format}_patch                     PATCH    ANY      ANY    /api/medical_opinions/{id}.{_format}
  _api_/patients{._format}_get_collection                         GET      ANY      ANY    /api/patients.{_format}
  _api_/patients/{id}{._format}_get                               GET      ANY      ANY    /api/patients/{id}.{_format}
  _api_/patients{._format}_post                                   POST     ANY      ANY    /api/patients.{_format}
  _api_/patients/{id}{._format}_patch                             PATCH    ANY      ANY    /api/patients/{id}.{_format}
  _api_/prescriptions{._format}_get_collection                    GET      ANY      ANY    /api/prescriptions.{_format}
  _api_/prescriptions/{id}{._format}_get                          GET      ANY      ANY    /api/prescriptions/{id}.{_format}
  _api_/prescriptions{._format}_post                              POST     ANY      ANY    /api/prescriptions.{_format}
  _api_/prescriptions/{id}{._format}_patch                        PATCH    ANY      ANY    /api/prescriptions/{id}.{_format}
  _api_/prescription_items/{id}{._format}_get                     GET      ANY      ANY    /api/prescription_items/{id}.{_format}
  _api_/prescription_items{._format}_post                         POST     ANY      ANY    /api/prescription_items.{_format}
