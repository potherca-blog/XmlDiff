XmlDiff
=======

Quick example on how to calculate the difference between two XML files, regardless of node order. Does not take node content or node attributes into account.


Given the following two XML files

**1.xml**:

    <xml>
        <twee>
            <d></d>
            <c></c>
        </twee>
        <een>
            <b></b>
            <a></a>
        </een>
    </xml>

**2.xml**: 

    <xml>
        <een>
            <a></a>
            <b></b>
        </een>
        <twee>
            <c></c>
            <d></d>
            <e></e>
        </twee>
    </xml>
    
The output of DiffXml::compare('1.xml','2.xml') would be:

    array (
        'In 1.xml but not in 2.xml' => '',
        'In 2.xml but not in 1.xml' => '<?xml version="1.0"?>
        <root><twee><e/></twee></root>
        ',
    )
    
For a fully functional example take a look at `index.php`    
    
<!-- EOF -->    
