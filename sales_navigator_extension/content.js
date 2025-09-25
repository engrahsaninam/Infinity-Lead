let buttonOriginalHtml = '';
let allFetchedLeads = [];
let isExporting = false;
let shouldStop = false;

function injectExportButton() {
    const container = document.querySelector('div.flex.align-items-stretch.border-bottom.p3');
    if (!container || document.getElementById('infinitylead-export-btn')) return;

    const btn = document.createElement('button');
    btn.id = 'infinitylead-export-btn';
    btn.style.cssText = `
        margin-right: 10px;
        background-color: rgb(17, 121, 252);
        border: none;
        color: white;
        white-space: nowrap;
        min-width: max-content;
        padding: 8px 12px;
        height: 32px;
        text-align: center;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        font-size: 14px;
        margin: 8px 8px;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.3s;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    `;
    btn.innerHTML = 'Export to InfinityLead';
    buttonOriginalHtml = 'Export to InfinityLead';
    btn.dataset.originalText = btn.innerText;
    btn.addEventListener('click', handleExportFlow);

    container.prepend(btn);
}

const observer = new MutationObserver(injectExportButton);
observer.observe(document.body, { childList: true, subtree: true });
injectExportButton();

function getCsrfToken() {
    const match = document.cookie.match(/(?:^|;\s*)JSESSIONID=([^;]+)/);
    return match ? match[1].replace(/^"|"$/g, '') : null;
}

function parseHashParams() {
    const hash = window.location.hash.substring(1); // remove '#'
    const params = {};
    hash.split('&').forEach(part => {
        const [key, val] = part.split('=');
        if (key) {
            try {
                params[key] = val ? decodeURIComponent(val) : '';
            } catch(e) {
                params[key] = val || '';
            }
        }
    });
    return params;
}
async function fetchLeadsViaInternalApi(start = 0, count = 25) {
    const csrfToken = getCsrfToken();

    let params;
    if (window.location.search.includes('query')) {
        params = Object.fromEntries(new URLSearchParams(window.location.search));
    } else {
        params = parseHashParams();
    }

    const queryParam = params.query || '';
    const sessionId = params.sessionId || '';



    const referrer = `https://www.linkedin.com/sales/search/people?query=${encodeURIComponent(queryParam)}&sessionId=${encodeURIComponent(sessionId)}`;

    const body =
        `q=searchQuery` +
        `&query=${queryParam}` +
        `&trackingParam=(sessionId:${sessionId})` +
        `&count=${count}` +
        `&decorationId=com.linkedin.sales.deco.desktop.searchv2.LeadSearchResult-14` +
        `&start=${start}`;

    const response = await fetch("https://www.linkedin.com/sales-api/salesApiLeadSearch", {
        headers: {
            "accept": "*/*",
            "accept-language": "en-US,en;q=0.9,de;q=0.8,es;q=0.7",
            "content-type": "application/x-www-form-urlencoded",
            "csrf-token": csrfToken,
            "priority": "u=1, i",
            "sec-ch-prefers-color-scheme": "dark",
            "sec-ch-ua": "\"Chromium\";v=\"136\", \"Google Chrome\";v=\"136\", \"Not.A/Brand\";v=\"99\"",
            "sec-ch-ua-mobile": "?0",
            "sec-ch-ua-platform": "\"macOS\"",
            "sec-fetch-dest": "empty",
            "sec-fetch-mode": "cors",
            "sec-fetch-site": "same-origin",
            "x-http-method-override": "GET",
            "x-li-lang": "en_US",
            "x-li-track": "{\"clientVersion\":\"2.0.4080\",\"mpVersion\":\"2.0.4080\",\"osName\":\"web\",\"timezoneOffset\":5,\"timezone\":\"Asia/Karachi\",\"deviceFormFactor\":\"DESKTOP\",\"mpName\":lighthouse-web,\"displayDensity\":1,\"displayWidth\":1920,\"displayHeight\":1080}",
            "x-restli-protocol-version": "2.0.0"
        },
        referrerPolicy: "strict-origin-when-cross-origin",
        referrer: referrer,
        body: body,
        method: "POST",
        mode: "cors",
        credentials: "include"
    });

    return await response.json();
}

async function handleExportFlow() {
    console.log('Export button clicked');
    const popup = document.getElementById('infinitylead-popup');
    if (popup) {
        popup.style.display = 'block';
    } else {
        console.warn('Popup element not found!');
    }

    popup.style.display = 'block';
    allFetchedLeads = [];

    const statusEl = document.getElementById('infinitylead-status');
    const input = document.getElementById('infinitylead-record-count');
    const progress = document.getElementById('infinitylead-progress');
    const downloadBtn = document.getElementById('download-csv');
    const processBtn = document.getElementById('infinitylead-process');


    statusEl.innerText = 'Fetching total...';
    input.style.display = 'none';
    progress.style.display = 'none';
    downloadBtn.style.display = 'none';

    const totalData = await fetchLeadsViaInternalApi(0, 1);
    const total = totalData?.paging?.total || 0;

    if (!total) {
        statusEl.innerText = 'Failed to fetch total records.';
        return;
    }

    statusEl.innerText = `Total Leads Found: ${total}`;
    input.style.display = 'block';
    input.value = '';
    input.focus();
    input.style.display = 'block';
    processBtn.style.display = 'block';
    input.value = '';
    input.focus();
    processBtn.onclick = async () => {
        const requested = parseInt(input.value);
        if (isNaN(requested) || requested <= 0 || requested > total || requested > 2500) {
            alert('Enter a valid number between 1 and 2500');
            return;
        }


        isExporting = true;
        shouldStop = false;
        input.style.display = 'none';
        processBtn.style.display = 'none';
        progress.style.display = 'block';
        document.getElementById('infinitylead-stop').style.display = 'block';
        progress.max = requested;
        progress.value = 0;
        statusEl.innerText = `Exporting up to ${requested} records...`;

        for (let i = 0; i < requested; i += 100) {
            if (shouldStop) break;
            const count = Math.min(100, requested - i);
            const result = await fetchLeadsViaInternalApi(i, count);
            const leads = result?.elements ?? [];
            allFetchedLeads = allFetchedLeads.concat(leads);
            progress.value = allFetchedLeads.length;
            const percent = Math.floor((allFetchedLeads.length / requested) * 100);
            document.getElementById('infinitylead-stop').innerText = `Stop & Download (${percent}%)`;
        }

        isExporting = false;
        document.getElementById('infinitylead-stop').style.display = 'none';
        statusEl.innerText = `Exported ${allFetchedLeads.length} records.`;
        downloadBtn.style.display = 'block';
        if (shouldStop) {
            triggerCSVDownload();
        }

    };

}


function convertToCSV(arr) {
    const mappedData = arr.map(item => {
        const firstName = item.firstName ?? '';
        const region = item.geoRegion ?? '';
        const lastName = item.lastName ?? '';
        const fullName = item.fullName ?? `${firstName} ${lastName}`;
        const title = item.currentPositions?.[0]?.title ?? '';
        const company = item.currentPositions?.[0]?.companyName ?? '';
        const profileId = item.objectUrn?.split(':').pop() ?? '';
        const profilePicture = item.profilePictureDisplayImage?.rootUrl && item.profilePictureDisplayImage?.artifacts?.[0]?.fileIdentifyingUrlPathSegment ? item.profilePictureDisplayImage.rootUrl + item.profilePictureDisplayImage.artifacts[0].fileIdentifyingUrlPathSegment : '';
        const profileUrl = (() => {
            const match = item.entityUrn?.match(/\(([^)]+)\)/);
            if (match && match[1]) {
                const id = match[1].split(',')[0];
                return `https://www.linkedin.com/in/${id}`;
            }
            return '';
        })();
        return {
            name: fullName,
            first_name: firstName,
            last_name: lastName,
            title: title,
            company: company,
            profile: profilePicture,
            url: profileUrl,
            region: region,
        };
    });

    const keys = [
        "name",
        "first_name",
        "last_name",
        "title",
        "company",
        "profile",
        "url",
        "region",
    ];

    const lines = mappedData.map(obj =>
        keys.map(k => `"${(obj[k] ?? '').toString().replace(/"/g, '""')}"`).join(',')
    );

    return keys.join(',') + '\n' + lines.join('\n');
}

function injectPopupHTML() {
    const popup = document.createElement('div');
    popup.innerHTML = `
        <div id="infinitylead-popup-overlay" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);display:none;z-index:9998;"></div>
        <div id="infinitylead-popup" style="
            display:none;
            position:fixed;
            top:50%;
            left:50%;
            transform:translate(-50%, -50%);
            background:white;
            padding:20px 25px;
            border-radius:10px;
            box-shadow:0 4px 20px rgba(0,0,0,0.2);
            z-index:9999;
            width:300px;
            font-family:Arial, sans-serif;
        ">
            <button id="infinitylead-close" style="
                position:absolute;
                top:8px;
                right:10px;
                border:none;
                background:transparent;
                font-size:25px;
                cursor:pointer;
                color:#888;
            ">&times;</button>


            <center>
                <img style="padding-top: 30px;padding-bottom: 30px" height="75" src="data:image/png;base64,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"  alt="" />
            </center>
            
            <p id="infinitylead-status" style="text-align: center;margin:0 0 10px;font-weight:bold;">Fetching total...</p>
            <input type="number" id="infinitylead-record-count" placeholder="Enter number to export" style="padding:8px;width:100%;box-sizing:border-box;border:1px solid #ccc;border-radius:4px;margin-bottom:10px;" />
            

            <button id="infinitylead-process" style="background:#28a745;color:white;padding:8px 12px;border:none;border-radius:4px;cursor:pointer;width:100%;margin-bottom:10px;display:none;font-weight:bold;">Process</button>

            
            <progress id="infinitylead-progress" max="100" value="0" style="width:100%;display:none;margin-bottom:10px;"></progress>
            
            <button id="infinitylead-stop" style="margin-top:10px;background:#dc3545;color:white;padding:8px 12px;border:none;border-radius:4px;cursor:pointer;width:100%;display:none;font-weight:bold;">Stop & Download</button>
            <button id="download-csv" style="background:rgb(17, 121, 252);color:white;padding:8px 12px;border:none;border-radius:4px;cursor:pointer;width:100%;display:none;font-weight:bold;">Download</button>
        </div>
    `;
    document.body.appendChild(popup);
    document.getElementById('infinitylead-close').addEventListener('click', () => {
        document.getElementById('infinitylead-popup').style.display = 'none';
        const overlay = document.getElementById('infinitylead-popup-overlay');
        if (overlay) overlay.style.display = 'none';
    });
}

injectPopupHTML();


document.getElementById('download-csv').addEventListener('click', () => {
    if (!allFetchedLeads.length) return alert("No data to export.");
    const csv = convertToCSV(allFetchedLeads);
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;

    const inputElement = document.getElementById('global-typeahead-search-input');
    const searchValue = inputElement?.value?.trim() || 'undefined';
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');

    a.download = `Infinity_Lead_${searchValue.replace(/\s+/g, '_')}_${year}_${month}_${day}.csv`;

    a.click();
    URL.revokeObjectURL(url);

    document.getElementById('infinitylead-popup').style.display = 'none';
    const overlay = document.getElementById('infinitylead-popup-overlay');
    if (overlay) overlay.style.display = 'none';

});
document.getElementById('infinitylead-stop').addEventListener('click', () => {
    shouldStop = true;
});
function triggerCSVDownload() {
    if (!allFetchedLeads.length) return alert("No data to export.");
    const csv = convertToCSV(allFetchedLeads);
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    const inputElement = document.getElementById('global-typeahead-search-input');
    const searchValue = inputElement?.value?.trim() || 'undefined';
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    a.download = `Infinity_Lead_${searchValue.replace(/\s+/g, '_')}_${year}_${month}_${day}.csv`;
    a.click();
    URL.revokeObjectURL(url);
    document.getElementById('infinitylead-popup').style.display = 'none';
    const overlay = document.getElementById('infinitylead-popup-overlay');
    if (overlay) overlay.style.display = 'none';
}