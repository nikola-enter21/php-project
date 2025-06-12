<?php
require_once 'core/BaseModel.php';
require_once 'core/Container.php';
require_once 'core/Database.php';
require_once 'core/Middleware.php';
require_once 'core/Request.php';
require_once 'core/Response.php';
require_once 'core/Router.php';
require_once 'core/Session.php';
require_once 'core/Database.php';

require_once 'app/middlewares/AuthMiddleware.php';
require_once 'app/middlewares/AdminMiddleware.php';

require_once 'app/models/QuoteModel.php';
require_once 'app/models/UserModel.php';
require_once 'app/models/LogModel.php';
require_once 'app/models/CollectionModel.php';

require_once 'app/controllers/AdminController.php';
require_once 'app/controllers/HomeController.php';
require_once 'app/controllers/QuoteController.php';
require_once 'app/controllers/UserController.php';
require_once 'app/controllers/CollectionController.php';
