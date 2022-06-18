<?php
//добавляем инклюдв файл где применяем константы php
/*include_once( ( $_SERVER['DOCUMENT_ROOT'] == '' ? '/var/www/php' : $_SERVER['DOCUMENT_ROOT'] ).'/var.php' );*/
namespace Variables
{

    class  constants{

        
        /**
         * @param namespace Cake\Variables  
         * @param  Class  cons 
         * @param  value  root
         */
        const ROOT = 'root';

        /**
         * @param namespace Cake\Variables  
         * @param  Class  cons 
         * @param  value  Qws31azx_Qws31azx
         */
        const PASS = 'Qws31azx_Qws31azx';


         /**
         * @param namespace Cake\Variables  
         * @param  Class  cons 
         * @param  value  db
         */
            const IP = 'db';
   /**
         * @param namespace Cake\Variables  
         * @param  Class  cons 
         * @param  value  db
         */
        const ENCODING_UTF_8 = 'utf-8';
          /**
         * @param namespace Cake\Variables  
         * @param  Class  cons 
         * @param  value  db
         */
        const SSL = 'ssl';
    }



}








namespace sql{
   define('sql\NAME','root');
define('sql\PASS', 'Qws31azx_Qws31azx');
define('sql\IP', '89.111.128.13');
define('sql\DB_NAME', 've');
define('sql\PATH_SERVER', '/var/www/php');
define('sql\LOCALHOST', '127.0.0.1');
define('sql\PORT', '3307'); 
}

namespace sqlAtomax{
define('sqlAtomax\NAME','root');
define('sqlAtomax\PASS', 'Qws31azx_Qws31azx');
define('sqlAtomax\IP', '89.111.128.13');
define('sqlAtomax\DB_NAME', 'atomax');
define('sqlAtomax\PATH_SERVER', '/var/www/php');
define('sqlAtomax\LOCALHOST', '127.0.0.1');
define('sqlAtomax\PORT', '6033');  
}

namespace minio{
define('minio\LOGIN','vhst');
define('minio\PASS','Qws31azx_Qws31azx_');
define('minio\PORT','9002');
define('minio\VERSION','latest');
define('minio\REGION','us-east-1');
}


namespace domain{
    define('domain\NAME','vhst.ru');
define('domain\PASS','Qws31azx_Qws31azx');
define('domain\PORT','9002');
define('domain\VERSION','latest');
define('domain\REGION','us-east-1');
}

namespace php_mailer{

}



namespace rules{
    define('rules\ALLOWURL', array(
    '/',
    '/connect/',
    '/signup/',
    '/support/'
));

}

namespace objct{
define('objct\siterootid', 1);
define('objct\encoding', 'utf-8');
define('objct\use_cache', false);
define('objct\cache_expire_time',1);
define('objct\dir_perms', 0755);
define('objct\file_perms', 0644);
define('objct\moduleUploadDir', '/module_upload/'); 
}


namespace atmx{
    define('atomax\TRUE', true);
define('atomax\FALSE', true);
define('atmx\locale', 'ru');
define('atmx\city_id', 927);
define('atmx\DelAll', 1);
define('atmx\DemoMode', 0);
define('atmx\LiteMode', 0);
define('atmx\DemoAtomax', 0);
define('atmx\LitePageLimit', 3);
define('atmx\NNovosteyNaStr', 10);
define('atmx\GoodsOnPage',20);
define('atmx\CountMapObjOnPage', 5);
define('atmx\include', 1);
}
namespace content_type{
  define( 'content_type\AMASK_REQUIRE', 1 );  
}


namespace ax_contx{
define('ax_contx\HTTP_NOT_FOUND', 404);
define('ax_contx\HTTP_SERVER_ERROR', 500);
define('ax_contx\ERROR_NO_RIGHTS', 403);  
define('ax_contx\STATUS_ALL', 0);
define('ax_contx\STATUS_DRAFT', 1);
define('ax_contx\STATUS_UNPUBLISHED', 1);
define('ax_contx\STATUS_PUBLISHED', 100);
define('ax_contx\STATUS_UNCACHE', 1);
define('ax_contx\STATUS_CACHE', 100);
define('ax_contx\STATUS_CACHETIME', 200);

define('ax_contx\RIGHT_READ', 'read_r');
define('ax_contx\RIGHT_MODIFY', 'modify_r');
define('ax_contx\RIGHT_CREATE', 'create_r');
define('ax_contx\RIGHT_STATUS', 'status_r');
define('ax_contx\DOMAINS', 'domains');
define('ax_contx\ROOT_CONTENT', 1);
}
// HTTP ERRORS


// content status values:

namespace RT{
$rtr=!empty($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '/var/www/php';
define('RT\PATH', $rtr);    
}

namespace CNTNT{
    define('CNTNT\MAIN', 'main');

define('CNTNT\TRUE', true);

define('CNTNT\FALSE', true);
}





?>