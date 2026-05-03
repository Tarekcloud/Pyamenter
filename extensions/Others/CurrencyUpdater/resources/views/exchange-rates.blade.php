<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Info Banner --}}
        <div class="fi-banner">
            <div class="fi-banner-icon" aria-hidden="true">i</div>
            <div>
                <div class="fi-banner-title">Exchange Rates Management</div>
                <div class="fi-banner-text">
                    Tip: Set one currency as <em>Default</em>. "Fetch Latest" uses it as the base (rate = 1.00000000).
                </div>
            </div>
        </div>

        {{-- Exchange Rates Table --}}
        <div class="fi-card">
            <div class="fi-toolbar">
                <strong class="fi-title">Manage Exchange Rates</strong>
            </div>

            <div class="fi-body">
                <div class="fi-table-wrap">
                    <table class="fi-table">
                        <thead>
                            <tr>
                                <th>Default</th>
                                <th>Code</th>
                                <th>Rate</th>
                                <th>Enabled</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($this->rates as $index => $rate)
                                <tr>
                                    <td>
                                        <input class="fi-radio" type="radio" name="default_code" 
                                               @checked($rate['is_default'])
                                               wire:click="setDefault({{ $index }})">
                                    </td>
                                    <td>
                                        <code class="fi-code">{{ $rate['code'] }}</code>
                                    </td>
                                    <td>
                                        <input type="number" step="0.00000001" min="0"
                                               class="fi-input"
                                               value="{{ rtrim(rtrim(number_format($rate['rate'], 8, '.', ''), '0'), '.') ?: '1' }}"
                                               wire:change="updateRate({{ $index }}, 'rate', $event.target.value)">
                                    </td>
                                    <td>
                                        <label class="fi-switch">
                                            <input type="checkbox" 
                                                   @checked($rate['enabled'])
                                                   wire:change="updateRate({{ $index }}, 'enabled', $event.target.checked)">
                                            <span class="fi-switch-dot"></span>
                                        </label>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="fi-empty">No currencies found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <p class="fi-tip">Tip: Set one currency as <em>Default</em>. "Fetch Latest" uses it as the base (rate = 1.00000000).</p>
            </div>
        </div>
    </div>

    <style>
        .fi-banner { 
            display: flex; 
            gap: 10px; 
            align-items: flex-start; 
            margin-bottom: 12px; 
            border: 1px solid #c7d2fe; 
            background: #eef2ff; 
            color: #1e1b4b; 
            border-radius: 10px; 
            padding: 12px 14px; 
        }
        .fi-banner-icon { 
            width: 22px; 
            height: 22px; 
            border-radius: 999px; 
            background: linear-gradient(135deg,#6366f1,#22d3ee); 
            color: #fff; 
            display:flex; 
            align-items:center; 
            justify-content:center; 
            font-weight: 800; 
            font-size: 13px; 
            margin-top: 2px; 
        }
        .fi-banner-title { 
            font-weight: 700; 
        }
        .fi-banner-text { 
            font-size: 13px; 
            opacity: .9; 
        }
        .fi-card { 
            border: 1px solid #e6e8ef; 
            background: #fff; 
            border-radius: 14px; 
            box-shadow: 0 8px 30px rgba(2,6,23,.06); 
            overflow: hidden; 
            margin-bottom: 16px; 
        }
        .fi-toolbar { 
            display:flex; 
            justify-content: space-between; 
            align-items:center; 
            padding: 14px 16px; 
            border-bottom: 1px solid #e6e8ef; 
            background: linear-gradient(90deg, rgba(99,102,241,.08), rgba(34,211,238,.08)); 
        }
        .fi-title { 
            color:#0f172a; 
        }
        .fi-body { 
            padding: 14px 16px 18px; 
        }
        .fi-table-wrap { 
            overflow:auto; 
        }
        .fi-table { 
            width:100%; 
            border-collapse: collapse; 
        }
        .fi-table th, .fi-table td { 
            padding: 12px 10px; 
            border-bottom: 1px solid #e6e8ef; 
            text-align: left; 
        }
        .fi-table th { 
            font-size: 12px; 
            text-transform: uppercase; 
            letter-spacing: .5px; 
            color: #667085; 
        }
        .fi-code { 
            background: #f1f5f9; 
            padding: 4px 8px; 
            border-radius: 8px; 
            color: #0f172a; 
        }
        .fi-input { 
            width: 160px; 
            color: #0f172a; 
            padding: 10px 12px; 
            border-radius: 8px; 
            border: 1px solid #e6e8ef; 
            background: #fff; 
            outline: none; 
        }
        .fi-switch { 
            width: 46px; 
            height: 26px; 
            display: inline-block; 
            position: relative; 
            border-radius: 999px; 
            background: #e5e7eb; 
            border: 1px solid #e6e8ef; 
            vertical-align: middle; 
        }
        .fi-switch input { 
            display: none; 
        }
        .fi-switch-dot { 
            position: absolute; 
            top: 2px; 
            left: 2px; 
            width: 20px; 
            height: 20px; 
            border-radius: 50%; 
            background: #fff; 
            transition: .2s ease all; 
            box-shadow: 0 1px 2px rgba(0,0,0,.1); 
        }
        .fi-switch input:checked + .fi-switch-dot { 
            transform: translateX(20px); 
            background: #22c55e; 
        }
        .fi-radio { 
            width: 18px; 
            height: 18px; 
            accent-color: #6366f1; 
        }
        .fi-empty { 
            color: #475569; 
        }
        .fi-tip { 
            color: #667085; 
            font-size: 13px; 
            margin-top: 10px; 
        }
    </style>

</x-filament-panels::page>
