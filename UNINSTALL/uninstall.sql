#########################################################################################
# Login als Kunde (Encrypted Master Passwort) 2.8.0 Zen-Cart 1.5.6 - 2019-06-15 - webchills
# !!!! UNINSTALL : NUR AUSFÜHREN, WENN SIE DAS MODUL KOMPLETT ENTFERNEN WOLLEN !!!!
#########################################################################################

DELETE FROM configuration WHERE configuration_key IN ('EMP_LOGIN_ADMIN_ID','EMP_LOGIN_ADMIN_PROFILE_ID');
DELETE FROM configuration_language WHERE configuration_key IN ('EMP_LOGIN_ADMIN_ID','EMP_LOGIN_ADMIN_PROFILE_ID');