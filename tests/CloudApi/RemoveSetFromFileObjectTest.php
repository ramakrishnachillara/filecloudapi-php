<?php

namespace CodeLathe\FileCloudApi\Tests\CloudApi;

use codelathe\fccloudapi\CloudAPI;
use codelathe\fccloudapi\CommandRecord;
use CodeLathe\FileCloudApi\Tests\Fixtures\AccessibleCloudApi;
use PHPUnit\Framework\TestCase;

class RemoveSetFromFileObjectTest extends TestCase
{
    public function testOnSuccess()
    {
        $serverUrl = 'https://fcapi.example.com';
        $cloudApiMock = $this->getMockBuilder(AccessibleCloudApi::class)
            ->setConstructorArgs([$serverUrl])
            ->setMethods(['init', '__destruct', 'doPost'])
            ->getMock();

        $mockApiResponse = <<<RESPONSE
<commands>
    <command>
        <type>removesetfromfileobject</type>
        <result>1</result>
        <message>Metadata set (setId: 5ccafe12adccf621f80342e6) was successfully removed for File Object (/tester/textfile1.txt)</message>
    </command>
</commands>
RESPONSE;
        $mockApiRequest = $this->getValidApiRequest();

        $cloudApiMock->method('doPost')
            ->with("{$serverUrl}/core/removesetfromfileobject", http_build_query($mockApiRequest))
            ->willReturn($mockApiResponse);

        /** @var CloudAPI $cloudApiMock */
        /** @var CommandRecord $commandRecord */
        $commandRecord = $cloudApiMock->removeSetFromFileObject(...array_values($mockApiRequest));
        $this->assertEquals(1, $commandRecord->getResult());
        $this->assertEquals('removesetfromfileobject', $commandRecord->getType());
        $this->assertEquals(
            'Metadata set (setId: 5ccafe12adccf621f80342e6) was successfully removed for File Object (/tester/textfile1.txt)',
            $commandRecord->getMessage()
        );
    }

    public function testOnFailure()
    {
        $serverUrl = 'https://fcapi.example.com';
        $cloudApiMock = $this->getMockBuilder(AccessibleCloudApi::class)
            ->setConstructorArgs([$serverUrl])
            ->setMethods(['init', '__destruct', 'doPost'])
            ->getMock();

        $mockApiResponse = <<<RESPONSE
<commands>
    <command>
        <type>removesetfromfileobject</type>
        <result>0</result>
        <message>Failed to remove Set Definition (setId: 5ccafe12adccf621f80342e7) for File Object (/tester/textfile1.txt). Reason: Incorrect set id provided</message>
    </command>
</commands>
RESPONSE;
        $mockApiRequest = $this->getValidApiRequest();
        $mockApiRequest['setid'] = '5ccafe12adccf621f80342e7';    // Pretend this is an invalid id


        $cloudApiMock->method('doPost')
            ->with("{$serverUrl}/core/removesetfromfileobject", http_build_query($mockApiRequest))
            ->willReturn($mockApiResponse);

        /** @var CloudAPI $cloudApiMock */
        /** @var CommandRecord $commandRecord */
        $commandRecord = $cloudApiMock->removeSetFromFileObject(...array_values($mockApiRequest));
        $this->assertEquals(0, $commandRecord->getResult());
        $this->assertEquals('removesetfromfileobject', $commandRecord->getType());
        $this->assertEquals(
            'Failed to remove Set Definition (setId: 5ccafe12adccf621f80342e7) for File Object (/tester/textfile1.txt). Reason: Incorrect set id provided',
            $commandRecord->getMessage()
        );
    }

    private function getValidApiRequest()
    {
        return [
            'fullpath' => '/tester/textfile1.txt',
            'setid' => '5ccafe12adccf621f80342e6',
        ];
    }
}