<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use App\Libraries\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\AppModel;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    // protected $session;
    #[\Override]
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
    }

    public static function isInternetExplorer(): bool
    {
        $agent = \Config\Services::request();
        if (method_exists($agent, 'getUserAgent')) {
            $agent = $agent->getUserAgent();
            return $agent->getBrowser() === 'Internet Explorer';
        }
        return false;
    }

    public static function getJurisdictions(): array
    {
        $jurisdictions = trim(($_ENV['app_jurisdiction'] ?? '') . ',' . ($_ENV['app_jurisdiction_development'] ?? ''), ',');
        $jurisdictions = explode(',', $jurisdictions);
        sort($jurisdictions);
        return $jurisdictions;
    }

    public static function getModelNamespace(object $controller, string $model): object
    {
        $controller = $controller::class;
        $controller = explode('\\', $controller);
        array_pop($controller);
        array_pop($controller);
        $controller[] = 'Models';
        $controller = implode('\\', $controller);
        $modelNamespace = $controller . '\\' . $model;
        if (!class_exists($modelNamespace)) {
            $modelNamespace = 'App\\Models\\' . $model;

        }
        return new $modelNamespace();
    }

    public static function getProductionJurisdictions(): array
    {
        $jurisdictions = trim(($_ENV['app_jurisdiction'] ?? ''), '|');
        $jurisdictions = explode('|', $jurisdictions);
        sort($jurisdictions);
        return $jurisdictions;
    }

    public static function isLive(): bool
    {
        return (ENVIRONMENT === 'development');
    }

    public static function isOnline(): bool
    {
        if (static::isLive() && gethostbyname('unpkg.com') === 'unpkg.com') {
            return false;
        } else {
            return true;
        }
    }

    public static function lastUpdatedDate(): string
    {
        date_default_timezone_set('America/New_York');
        $AppModel = new AppModel();
        return $AppModel->getLastUpdated()[0]->fulldate ?? '';
    }

    public static function lastUpdatedVersion(): string
    {
        $AppModel = new AppModel();
        return $AppModel->getLastUpdated()[0]->lastrefreshversion ?? '';
    }

    protected function getIdInt(int|string $id): int|string
    {
        if (static::isLive() && is_string($id) && preg_match('/^\d{1,9}$/', $id) === 1) {
            $id = (int) $id;
        }
        return $id;
    }

    protected function isError(string $title = 'Error'): void
    {
        $this->response->setStatusCode(404);
        echo view('core/header', ['title' => $title]);
        echo view('core/error');
        echo view('core/footer');
    }

    protected function noRecord(string $title = 'No Record'): void
    {
        $this->response->setStatusCode(404);
        echo view('core/header', ['title' => $title]);
        echo view('core/norecord');
        echo view('core/footer');
    }
}
