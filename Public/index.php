<?php
    
    include(dirname(dirname(__FILE__)) . "/PowerCMS/autoload.php");
        
    use PowerCMS\Exception\PowerExceptionFatalError;
    use PowerCMS\Exception\PowerExceptionNotFound; 
    use PowerCMS\Helper\PowerHelperApplication;   
    use PowerCMS\Helper\PowerHelperApi;
    
    PowerHelperApi::setUrlModule("http://api.powercms.dev/");
           
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