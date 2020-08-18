<?php
namespace Lamansky\Doctrine;
use Doctrine\ORM\Decorator\EntityManagerDecorator;
use Doctrine\Persistence\ObjectRepository;

abstract class ProjectEntityManager extends EntityManagerDecorator {
    abstract protected function getEntityNamespacePrefix () : string;
    abstract protected function getProxyNamespacePrefix () : string;

    protected function getDriver (bool $is_dev_env) : string {
        return 'pdo_mysql';
    }

    protected function getCharset (bool $is_dev_env) : string {
        return 'utf8';
    }

    public function __construct (string $db_host,
                                 string $db_user,
                                 string $db_pass,
                                 string $db_name,
                                 bool $is_dev_env,
                                 string $entity_dir_path,
                                 string $proxy_dir_path
                                 ) {
        $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
            [$entity_dir_path],
            $is_dev_env
        );

        $config->setProxyDir($proxy_dir_path);
        $config->setProxyNamespace($this->getProxyNamespacePrefix());

        parent::__construct(\Doctrine\ORM\EntityManager::create(
            [
                'driver'   => $this->getDriver($is_dev_env),
                'host'     => $db_host,
                'user'     => $db_user,
                'password' => $db_pass,
                'dbname'   => $db_name,
                'charset'  => $this->getCharset($is_dev_env),
            ],
            $config
        ));
    }

    public function find ($entityName, $id, $lockMode = null, $lockVersion = null) : ?object {
        return parent::find($this->getEntityNamespacePrefix() . $entityName, $id, $lockMode, $lockVersion);
    }

    public function getRepository ($entityName) : ?ObjectRepository {
        return parent::getRepository($this->getEntityNamespacePrefix() . $entityName);
    }
}
