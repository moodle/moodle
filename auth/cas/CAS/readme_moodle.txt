Description of phpCAS 1.1.2 library import into Moodle

Our changes:
 * CAS.php - fix notice of $_SERVER['REQUEST_URI'] not being defined under IIS
   (we can remove this change when we upgrade to a phpCAS version that has
    https://issues.jasig.org/browse/PHPCAS-81 fixed).

iarenaza
