<?php
class HelloTest extends AbstractTest {
    /**
     * Test "hello" API method
     */
    public function testHello() {
        $client = $this->getHttpClient();
        $this->assertEquals(200, $client->get('/v1/')->getStatusCode());
        $this->assertEquals(405, $client->post('/v1/')->getStatusCode());
    }


    /**
     * Test Not Found methods
     */
    public function testNotFound() {
        $client = $this->getHttpClient();
        $this->assertEquals(404, $client->get('/v1')->getStatusCode());
        $this->assertEquals(404, $client->post('/v1')->getStatusCode());
        $this->assertEquals(404, $client->get('/v1/hello')->getStatusCode());
        $this->assertEquals(404, $client->post('/v1/exposed/')->getStatusCode());
    }
}
