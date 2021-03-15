function showRss(feedUrl) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', feedUrl);

    xhr.onreadystatechange = function () {
        const DONE = 4;
        const OK = 200;
        let parser;
        let xmlDoc;
        if (xhr.readyState === DONE) {
            if (xhr.status === OK) {
                let result = "";
                if (window.DOMParser) {
                    parser = new DOMParser();
                    xmlDoc = parser.parseFromString(xhr.responseText, "text/xml");
                } else // Internet Explorer
                {
                    xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
                    xmlDoc.async = false;
                    xmlDoc.loadXML(xhr.responseText);
                }

                // scripts.robotess.net
                const maxCount = xmlDoc.getElementsByTagName("item") ? xmlDoc.getElementsByTagName("item").length : 0;
                if (maxCount === 0) {
                    return;
                }

                const items = xmlDoc.getElementsByTagName("item");

                for (let number = 0; number < Math.min(3, maxCount); ++number) {
                    const template = `
<h2>${items[number].getElementsByTagName("title")[0].childNodes[0].nodeValue}<br />
                <small>${items[number].getElementsByTagName("pubDate")[0].childNodes[0].nodeValue} &bull; <a href="${items[number].getElementsByTagName("link")[0].childNodes[0].nodeValue}" target="_blank">permalink</a></small></h2>
                <blockquote>${items[number].getElementsByTagName("description")[0].childNodes[0].nodeValue}</blockquote>`;
                    result += template;
                }

                document.getElementById("rss-feed-robotess-net").innerHTML = result;
            } else {
                console.log('Error: ' + xhr.status); // An error occurred during the request.
            }
        }
    };

    xhr.send(null);
}