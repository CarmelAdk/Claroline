<?php

namespace Claroline\CoreBundle\Controller;

use Claroline\CoreBundle\Library\Testing\FunctionalTestCase;
use Claroline\CoreBundle\Tests\DataFixtures\LoadResourceTypeData;
use Claroline\CoreBundle\DataFixtures\LoadMimeTypeData;

class FileControllerTest extends FunctionalTestCase
{
    /** @var string */
    private $upDir;

    /** @var string */
    private $stubDir;

    public function setUp()
    {
        parent::setUp();
        $this->loadFixture(new LoadResourceTypeData());
        $this->loadUserFixture();
        $this->client->followRedirects();
        $ds = DIRECTORY_SEPARATOR;
        $this->stubDir = __DIR__ . "{$ds}..{$ds}Stub{$ds}files{$ds}";
        $this->upDir = $this->client->getContainer()->getParameter('claroline.files.directory');
        $this->cleanDirectory($this->upDir);
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->cleanDirectory($this->upDir);
    }

    public function testUpload()
    {
        $this->logUser($this->getFixtureReference('user/admin'));
        $originalPath = $this->stubDir . 'originalFile.txt';
        $crawler = $this->uploadFile($originalPath);
        $crawler = $this->client->request('GET', '/resource/directory');
        $this->assertEquals(1, $crawler->filter('.row_resource')->count());
        $this->assertEquals(1, count($this->getUploadedFiles()));
    }

    public function testDownload()
    {
        $this->logUser($this->getFixtureReference('user/admin'));
        $originalPath = $this->stubDir . 'originalFile.txt';
        $crawler = $this->uploadFile($originalPath);
        $crawler = $this->client->request('GET', '/resource/directory');
        $link = $crawler->filter('.link_resource_view')->eq(0)->link();
        $this->client->click($link);
        $headers = $this->client->getResponse()->headers;
        $this->assertTrue($headers->contains('Content-Disposition', 'attachment; filename=originalFile.txt'));
    }

    public function testDelete()
    {
        $this->logUser($this->getFixtureReference('user/admin'));
        $originalPath = $this->stubDir . 'originalFile.txt';
        $crawler = $this->uploadFile($originalPath);
        $crawler = $this->client->request('GET', '/resource/directory');
        $link = $crawler->filter('.link_delete_resource')->eq(0)->link();
        $crawler = $this->client->click($link);
        $this->assertEquals(0, $crawler->filter('.row_resource')->count());
        $this->assertEquals(0, count($this->getUploadedFiles()));
    }

    private function uploadFile($filePath)
    {
        $crawler = $this->client->request('GET', '/resource/directory');

        $form = $crawler->filter('input[type=submit]')->form();
        $form['select_resource_form[type]'] = $this->getFixtureReference('resource_type/file')->getId();
        $crawler = $this->client->submit($form);
        $form = $crawler->filter('input[type=submit]')->form();
        return $this->client->submit($form, array('file_form[name]' => $filePath));
    }

    private function getUploadedFiles()
    {
        $iterator = new \DirectoryIterator($this->upDir);
        $uploadedFiles = array();

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() !== 'placeholder') {
                $uploadedFiles[] = $file->getFilename();
            }
        }

        return $uploadedFiles;
    }

    private function cleanDirectory($dir)
    {
        $iterator = new \DirectoryIterator($dir);

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() !== 'placeholder'
                && $file->getFilename() !== 'originalFile.txt'
                && $file->getFilename() !== 'originalZip.zip'
            ) {
                chmod($file->getPathname(), 0777);
                unlink($file->getPathname());
            }
        }
    }
}
