<?php

use PHPUnit\Framework\TestCase;

use LeadpagesWP\Helpers\LeadpageType;

class LeadpageTypeTest extends TestCase
{
    protected $html;

    public function setUp()
    {
        $this->html = <<<HTML
            <!DOCTYPE html>
            <html>
                <head>
                    <meta name="leadpages-served-by" content="leadpages" />
                    <base href="http://localhost:8888/wordpress/">
                </head>
                <body>
                    <p class="message">Hello World!</p>
                    <p>Hello World!</p>
                </body>
            </html>
HTML;
    }

    public function testLookupMetaByNameStaticMethod()
    {
        $dom = new \DOMDocument();
        $dom->loadHtml($this->html);
        $meta_value = LeadpageType::lookupMetaByName($dom, 'leadpages-served-by'); 
        $this->assertEquals($meta_value, 'leadpages');
    }

    /**
     * @depends testLookupMetaByNameStaticMethod
     */
    public function testUpdateMetaServedByContentWithStaticMethod()
    {
        $output = LeadpageType::modifyMetaServedBy($this->html, 'wordpress');

        $dom = new \DOMDocument;
        $dom->loadHTML($output);
        $meta_value = LeadpageType::lookupMetaByName($dom, 'leadpages-served-by'); 
        $this->assertEquals($meta_value, 'wordpress');
    }
    
    /**
     * @depends testLookupMetaByNameStaticMethod
     */
    public function testCreateMetaServedByContentWithStaticMethod()
    {
        $html = '<html><head></head></html>';
        $output = LeadpageType::modifyMetaServedBy($html, 'wordpress');

        $dom = new \DOMDocument;
        $dom->loadHTML($output);
        $meta_value = LeadpageType::lookupMetaByName($dom, 'leadpages-served-by'); 
        $this->assertEquals($meta_value, 'wordpress');
    }

    public function testCreateMetaTag()
    {
        $elem = LeadpageType::createMetaTag('leadpages-served-by', 'wordpress');
        $this->assertInstanceOf(\DOMElement::class, $elem);
    }
}

