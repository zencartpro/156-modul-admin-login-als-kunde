#########################################################################################
# Login als Kunde (Encrypted Master Passwort) 2.8.1 Zen-Cart 1.5.6 - 2020-04-07 - webchills
# !!!! UNINSTALL : NUR AUSFÜHREN, WENN SIE DAS MODUL KOMPLETT ENTFERNEN WOLLEN !!!!
#########################################################################################

DELETE FROM configuration WHERE configuration_key IN ('EMP_LOGIN_ADMIN_ID','EMP_LOGIN_ADMIN_PROFILE_ID','EMP_LOGIN_AUTOMATIC');
DELETE FROM configuration_language WHERE configuration_key IN ('EMP_LOGIN_ADMIN_ID','EMP_LOGIN_ADMIN_PROFILE_ID','EMP_LOGIN_AUTOMATIC');