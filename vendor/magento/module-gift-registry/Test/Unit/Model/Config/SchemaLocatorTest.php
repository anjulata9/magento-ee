<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GiftRegistry\Test\Unit\Model\Config;

use Magento\Framework\Module\Dir\Reader;
use Magento\GiftRegistry\Model\Config\SchemaLocator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SchemaLocatorTest extends TestCase
{
    /**
     * @var MockObject
     */
    protected $_moduleReaderMock;

    /**
     * @var SchemaLocator
     */
    protected $_model;

    protected function setUp(): void
    {
        $this->_moduleReaderMock = $this->createMock(Reader::class);
        $this->_moduleReaderMock->expects(
            $this->once()
        )->method(
            'getModuleDir'
        )->with(
            'etc',
            'Magento_GiftRegistry'
        )->willReturn(
            'schema_dir'
        );
        $this->_model = new SchemaLocator($this->_moduleReaderMock);
    }

    public function testGetSchema()
    {
        $this->assertEquals('schema_dir' . '/giftregistry.xsd', $this->_model->getSchema());
    }

    public function testGetPerFileSchema()
    {
        $this->assertNull($this->_model->getPerFileSchema());
    }
}
