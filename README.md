XmlDiff
=======

Quick example on how to calculate the difference between two XML files, regardless of node order. Also takes node  attributes into account. 

It does this by converting the XML in each file to an array, sorting the array and then creating XML from the sorted array. It then uses [PHP-Diff](https://github.com/phpspec/php-diff) to calculate and display the diff between the sorted XML.

You can [try it out here](http://xmldiff.herokuapp.com/).

---

Given the following two XML files

**1.xml**:

    <xml>
        <twee>
            <d/>
            <c an-attribute="another value"></c>
        </twee>
        <een>
            <b>
                Some more content
            </b>
            <a>
                Some content
            </a>
        </een>
    </xml>

**2.xml**: 

    <xml>
        <een>
            <a>
                Some content
            </a>
            <b>
                Some more content
            </b>
        </een>
        <twee>
            <c an-attribute="a different value" second-attribute="a value also" first-attribute="a value"></c>
            <d/>
            <e></e>
        </twee>
    </xml>

The output would be:


<table class="Differences DifferencesSideBySide" style="width: 100%;border-collapse: collapse;border-spacing: 0;empty-cells: show"><thead><tr><th colspan="2" style="text-align: left;border-bottom: 1px solid #000;background: #aaa;color: #000;padding: 4px">Old Version</th><th colspan="2" style="text-align: left;border-bottom: 1px solid #000;background: #aaa;color: #000;padding: 4px">New Version</th></tr></thead><tbody class="ChangeEqual"><tr><th style="text-align: right;background: #ccc;width: 4em;padding: 1px 2px;border-right: 1px solid #000;vertical-align: top;font-size: 13px">9</th><td class="Left" style="padding: 1px 2px;font-family: Consolas, monospace;font-size: 13px"><span>       &lt;/b&gt;</span> </td><th style="text-align: right;background: #ccc;width: 4em;padding: 1px 2px;border-right: 1px solid #000;vertical-align: top;font-size: 13px">9</th><td class="Right" style="padding: 1px 2px;font-family: Consolas, monospace;font-size: 13px"><span>       &lt;/b&gt;</span> </td></tr><tr><th style="text-align: right;background: #ccc;width: 4em;padding: 1px 2px;border-right: 1px solid #000;vertical-align: top;font-size: 13px">10</th><td class="Left" style="padding: 1px 2px;font-family: Consolas, monospace;font-size: 13px"><span> &lt;/een&gt;</span> </td><th style="text-align: right;background: #ccc;width: 4em;padding: 1px 2px;border-right: 1px solid #000;vertical-align: top;font-size: 13px">10</th><td class="Right" style="padding: 1px 2px;font-family: Consolas, monospace;font-size: 13px"><span> &lt;/een&gt;</span> </td></tr><tr><th style="text-align: right;background: #ccc;width: 4em;padding: 1px 2px;border-right: 1px solid #000;vertical-align: top;font-size: 13px">11</th><td class="Left" style="padding: 1px 2px;font-family: Consolas, monospace;font-size: 13px"><span> &lt;twee&gt;</span> </td><th style="text-align: right;background: #ccc;width: 4em;padding: 1px 2px;border-right: 1px solid #000;vertical-align: top;font-size: 13px">11</th><td class="Right" style="padding: 1px 2px;font-family: Consolas, monospace;font-size: 13px"><span> &lt;twee&gt;</span> </td></tr></tbody><tbody class="ChangeReplace"><tr><th style="text-align: right;background: #ccc;width: 4em;padding: 1px 2px;border-right: 1px solid #000;vertical-align: top;font-size: 13px">12</th><td class="Left" style="padding: 1px 2px;font-family: Consolas, monospace;font-size: 13px;background: #fe9"><span>   &lt;c an-attribute="a<del style="text-decoration: none;background: #fc0">nother value</del>"/&gt;</span> </td><th style="text-align: right;background: #ccc;width: 4em;padding: 1px 2px;border-right: 1px solid #000;vertical-align: top;font-size: 13px">12</th><td class="Right" style="padding: 1px 2px;font-family: Consolas, monospace;font-size: 13px;background: #fd8"><span>   &lt;c an-attribute="a<ins style="text-decoration: none;background: #fc0"> different value" first-attribute="a value" second-attribute="a value also</ins>"/&gt;</span></td></tr></tbody><tbody class="ChangeEqual"><tr><th style="text-align: right;background: #ccc;width: 4em;padding: 1px 2px;border-right: 1px solid #000;vertical-align: top;font-size: 13px">13</th><td class="Left" style="padding: 1px 2px;font-family: Consolas, monospace;font-size: 13px"><span>   &lt;d/&gt;</span> </td><th style="text-align: right;background: #ccc;width: 4em;padding: 1px 2px;border-right: 1px solid #000;vertical-align: top;font-size: 13px">13</th><td class="Right" style="padding: 1px 2px;font-family: Consolas, monospace;font-size: 13px"><span>   &lt;d/&gt;</span> </td></tr></tbody><tbody class="ChangeInsert"><tr><th style="text-align: right;background: #ccc;width: 4em;padding: 1px 2px;border-right: 1px solid #000;vertical-align: top;font-size: 13px"> </th><td class="Left" style="padding: 1px 2px;font-family: Consolas, monospace;font-size: 13px;background: #dfd"> </td><th style="text-align: right;background: #ccc;width: 4em;padding: 1px 2px;border-right: 1px solid #000;vertical-align: top;font-size: 13px">14</th><td class="Right" style="padding: 1px 2px;font-family: Consolas, monospace;font-size: 13px;background: #cfc"><ins style="text-decoration: none">   &lt;e/&gt;</ins> </td></tr></tbody><tbody class="ChangeEqual"><tr><th style="text-align: right;background: #ccc;width: 4em;padding: 1px 2px;border-right: 1px solid #000;vertical-align: top;font-size: 13px">14</th><td class="Left" style="padding: 1px 2px;font-family: Consolas, monospace;font-size: 13px"><span> &lt;/twee&gt;</span> </td><th style="text-align: right;background: #ccc;width: 4em;padding: 1px 2px;border-right: 1px solid #000;vertical-align: top;font-size: 13px">15</th><td class="Right" style="padding: 1px 2px;font-family: Consolas, monospace;font-size: 13px"><span> &lt;/twee&gt;</span> </td></tr><tr><th style="text-align: right;background: #ccc;width: 4em;padding: 1px 2px;border-right: 1px solid #000;vertical-align: top;font-size: 13px">15</th><td class="Left" style="padding: 1px 2px;font-family: Consolas, monospace;font-size: 13px"><span>&lt;/root&gt;</span> </td><th style="text-align: right;background: #ccc;width: 4em;padding: 1px 2px;border-right: 1px solid #000;vertical-align: top;font-size: 13px">16</th><td class="Right" style="padding: 1px 2px;font-family: Consolas, monospace;font-size: 13px"><span>&lt;/root&gt;</span> </td></tr><tr><th style="text-align: right;background: #ccc;width: 4em;padding: 1px 2px;border-right: 1px solid #000;vertical-align: top;font-size: 13px">16</th><td class="Left" style="padding: 1px 2px;font-family: Consolas, monospace;font-size: 13px"><span/> </td><th style="text-align: right;background: #ccc;width: 4em;padding: 1px 2px;border-right: 1px solid #000;vertical-align: top;font-size: 13px">17</th><td class="Right" style="padding: 1px 2px;font-family: Consolas, monospace;font-size: 13px"><span/> </td></tr></tbody></table>


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/potherca/xmldiff/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

