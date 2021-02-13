<?php

// using ldap bind
$ldaprdn  = 'pormeno';     // ldap rdn or dn
$ldappass = 'pormeno2016';  // associated password

// connect to ldap server
//$ldapconn = ldap_connect("adprueba.cl")
$ldapconn = ldap_connect("172.21.5.110")
    or die("Could not connect to LDAP server.");

if ($ldapconn) {

    // binding to ldap server
    $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);

    // verify binding
    if ($ldapbind) {
        echo "LDAP bind successful...";
    } else {
        echo "LDAP bind failed...";
    }

}

?>