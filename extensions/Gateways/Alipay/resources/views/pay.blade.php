<div class="mt-2">
    <div class="p-6 rounded-lg">
        <h3 class="text-lg font-semibold mb-4 text-center">支付宝</h3>
        <div class="flex justify-center mb-4">
            <div id="qrcode" class="bg-white p-4 rounded-lg"></div>
        </div>
        <div class="text-center">
            <p class="mb-2">请使用支付宝扫描上方二维码完成支付</p>
        </div>
    </div>
</div>

@script
<script>
    (function () {
        window.paymentCheckInterval && clearInterval(window.paymentCheckInterval);

        async function loadQR() {
            if (window.encodeQR) return window.encodeQR;
            const {
                default: encodeQR
            } = await import('/assets/gateway/index.min.js');
            return window.encodeQR = encodeQR;
        }

        let qrTries = 0;
        async function generateQR() {
            const qr = document.getElementById('qrcode');
            if (!qr) {
                if (qrTries++ < 20) setTimeout(generateQR, 100);
                return;
            }
            const encodeQR = await loadQR();
            const svg = encodeQR('{{ $qr_code }}', 'svg', {
                ecc: 'high',
                scale: 8,
                border: 2
            });
            const element = new DOMParser().parseFromString(svg, 'image/svg+xml').documentElement;
            Object.assign(element.style, {
                width: '200px',
                height: '200px',
                display: 'block',
                margin: '0 auto'
            });
            qr.innerHTML = '';

            qr.appendChild(element);
        }

        function checkPayment() {
            window.paymentCheckInterval = setInterval(async () => {
                try {
                    const response = await fetch("{{ route('extensions.gateways.alipay.status', ['invoice' => $invoice->id]) }}", {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await response.json();
                    if (data.paid) {
                        clearInterval(window.paymentCheckInterval);
                        window.location.reload();
                    }
                } catch (_) { }
            }, 3000);
        }

        document.readyState === 'loading' ?
            document.addEventListener('DOMContentLoaded', () => {
                generateQR();
                checkPayment();
            }) :
            (generateQR(), checkPayment());

        window.addEventListener('beforeunload', () => window.paymentCheckInterval && clearInterval(window.paymentCheckInterval));
    })();
</script>
@endscript
