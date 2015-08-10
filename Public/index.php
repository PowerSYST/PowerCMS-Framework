<?php
    
    include(dirname(dirname(__FILE__)) . "/App/Src/Vendor/PowerCMS/autoload.php");
        
    use PowerCMS\Exception\PowerExceptionFatalError;
    use PowerCMS\Exception\PowerExceptionNotFound; 
    use PowerCMS\Helper\PowerHelperApplication;   
           
    try {     
        $app = new PowerHelperApplication(); 
        $app->run(POWERCMS_MODULE);     
    } catch (PowerExceptionFatalError $ex) {        
        echo $ex->getMessage();
    } catch (PowerExceptionNotFound $ex) {
        echo $ex->getMessage();
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }