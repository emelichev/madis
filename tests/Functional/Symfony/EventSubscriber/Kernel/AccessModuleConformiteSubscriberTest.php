<?php

declare(strict_types=1);

namespace App\Tests\Functional\Symfony\EventSubscriber\Kernel;

use App\Domain\User\Repository\User as UserRepository;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AccessModuleConformiteSubscriberTest extends WebTestCase
{
    use RecreateDatabaseTrait;

    public function testDenyAccessToConformiteTraitementForLecteurUsersOnCollectivityWithNoConformiteModule()
    {
        $client = static::createClient();
        self::populateDatabase();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser       = $userRepository->findOneOrNullByEmail('lecteur@awkan.fr');

        $client->loginUser($testUser);
        $url = $client->getContainer()->get('router')->generate('registry_conformite_traitement_list', [], UrlGeneratorInterface::RELATIVE_PATH);

        $client->request('GET', $url);
        $this->assertResponseStatusCodeSame(403);
    }

    public function testAllowAccessToConformiteTraitementForLecteurUsersOnCollectivityWithConformiteModule()
    {
        $client = static::createClient();
        self::populateDatabase();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser       = $userRepository->findOneOrNullByEmail('reader-awkan@awkan.fr');

        $client->loginUser($testUser);
        $url = $client->getContainer()->get('router')->generate('registry_conformite_traitement_list', [], UrlGeneratorInterface::RELATIVE_PATH);

        $client->request('GET', $url);
        $this->assertResponseIsSuccessful();
    }

    public function testAllowAccessToConformiteTraitementForAdminUsers()
    {
        $client = static::createClient();
        self::populateDatabase();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser       = $userRepository->findOneOrNullByEmail('admin@awkan.fr');

        $client->loginUser($testUser);
        $url = $client->getContainer()->get('router')->generate('registry_conformite_traitement_list', [], UrlGeneratorInterface::RELATIVE_PATH);
        $client->request('GET', $url);
        $this->assertResponseIsSuccessful();
    }
}
