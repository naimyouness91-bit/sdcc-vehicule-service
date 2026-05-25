@extends('layouts.app')

@section('title', 'SDCC - Nouvelle Zone')

@section('content')
<div style="background: linear-gradient(135deg, #f5f7fa 0%, #f0f3f7 100%); min-height: 100vh; padding: 16px 20px;">
    <div style="max-width: 900px; width: 100%; margin: 0 auto; padding: 0; box-sizing: border-box;">

        {{-- Header --}}
        <div style="margin-bottom: 32px;">
            <a href="{{ route('zones.index') }}" style="
                display: inline-flex; align-items: center; gap: 8px;
                color: #667085; font-size: 14px; text-decoration: none;
                margin-bottom: 16px; transition: color 0.2s;
            " onmouseover="this.style.color='#00a86b'" onmouseout="this.style.color='#667085'">
                <i class="fas fa-arrow-left"></i> Retour aux zones
            </a>
            <h1 style="margin: 0; color: #1a2332; font-size: 28px; font-weight: 700; display: flex; align-items: center; gap: 12px;">
                <i class="fas fa-plus-circle" style="color: #00d084; font-size: 30px;"></i>
                Nouvelle Zone
            </h1>
            <p style="margin: 8px 0 0; color: #667085; font-size: 15px;">
                Créez une nouvelle zone de planification pour organiser vos ressources.
            </p>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div style="
                background: linear-gradient(135deg,#fff3e0,#ffe0b2);
                border-left: 4px solid #e65100; color: #bf360c;
                padding: 16px 20px; border-radius: 12px; margin-bottom: 24px;
                box-shadow: 0 4px 12px rgba(230,81,0,0.1);
            ">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;font-weight:600;">
                    <i class="fas fa-exclamation-triangle"></i> Veuillez corriger les erreurs suivantes :
                </div>
                <ul style="margin:0;padding-left:20px;">
                    @foreach ($errors->all() as $error)
                        <li style="font-size:14px;margin-bottom:4px;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form Card --}}
        <div style="background: white; border-radius: 16px; padding: 36px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
            <form method="POST" action="{{ route('zones.store') }}">
                @csrf

                {{-- Name --}}
                <div style="margin-bottom: 24px;">
                    <label style="display:block;font-size:14px;font-weight:600;color:#1a2332;margin-bottom:8px;">
                        <i class="fas fa-tag" style="color:#00d084;margin-right:6px;"></i>
                        Nom de la zone <span style="color:#e65100;">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="Ex : Zone Nord, Zone Casablanca..."
                           style="
                               width: 100%; padding: 13px 16px; border-radius: 10px;
                               border: 1.5px solid {{ $errors->has('name') ? '#e65100' : '#e5e7eb' }};
                               font-size: 15px; color: #1a2332; outline: none;
                               transition: border-color 0.2s; box-sizing: border-box;
                               background: {{ $errors->has('name') ? '#fff8f0' : 'white' }};
                           "
                           onfocus="this.style.borderColor='#00d084'"
                           onblur="this.style.borderColor='{{ $errors->has('name') ? '#e65100' : '#e5e7eb' }}'">
                    @error('name')
                        <p style="margin:6px 0 0;font-size:13px;color:#e65100;">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Description --}}
                <div style="margin-bottom: 24px;">
                    <label style="display:block;font-size:14px;font-weight:600;color:#1a2332;margin-bottom:8px;">
                        <i class="fas fa-align-left" style="color:#00d084;margin-right:6px;"></i>
                        Description
                    </label>
                    <textarea name="description" rows="4"
                              placeholder="Décrivez cette zone (localisation, périmètre, activité...)"
                              style="
                                  width: 100%; padding: 13px 16px; border-radius: 10px;
                                  border: 1.5px solid {{ $errors->has('description') ? '#e65100' : '#e5e7eb' }};
                                  font-size: 15px; color: #1a2332; outline: none; resize: vertical;
                                  transition: border-color 0.2s; box-sizing: border-box; font-family: inherit;
                              "
                              onfocus="this.style.borderColor='#00d084'"
                              onblur="this.style.borderColor='#e5e7eb'">{{ old('description') }}</textarea>
                    @error('description')
                        <p style="margin:6px 0 0;font-size:13px;color:#e65100;">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Status --}}
                <div style="margin-bottom: 32px;">
                    <label style="display:block;font-size:14px;font-weight:600;color:#1a2332;margin-bottom:12px;">
                        <i class="fas fa-toggle-on" style="color:#00d084;margin-right:6px;"></i>
                        Statut <span style="color:#e65100;">*</span>
                    </label>

                    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                        @foreach([
                            'active' => ['Active', '#00d084', '#00613e', '#d4f0e5', 'fa-check-circle'],
                            'in_progress' => ['En cours', '#FFA726', '#bf360c', '#fff3e0', 'fa-hourglass-half'],
                            'inactive' => ['Inactive', '#bdbdbd', '#757575', '#f5f5f5', 'fa-times-circle']
                        ] as $value => $props)

                        <label style="
                            flex: 1; min-width: 120px; cursor: pointer;
                            border: 2px solid {{ old('status') === $value ? $props[1] : '#e5e7eb' }};
                            border-radius: 10px; padding: 14px 16px;
                            display: flex; align-items: center; gap: 10px;
                            background: {{ old('status') === $value ? $props[3] : 'white' }};
                            transition: all 0.2s;
                        ">
                            <input type="radio" name="status" value="{{ $value }}"
                                   {{ old('status', 'active') === $value ? 'checked' : '' }}
                                   style="accent-color: {{ $props[1] }}; width: 16px; height: 16px;"
                                   onchange="document.querySelectorAll('[data-status-label]').forEach(el => { el.parentElement.style.border = '2px solid #e5e7eb'; el.parentElement.style.background = 'white'; }); this.parentElement.style.border = '2px solid {{ $props[1] }}'; this.parentElement.style.background = '{{ $props[3] }}';">

                            <i class="fas {{ $props[4] }}" style="color: {{ $props[1] }};"></i>
                            <span data-status-label style="font-size:14px;font-weight:600;color:{{ $props[2] }};">
                                {{ $props[0] }}
                            </span>
                        </label>

                        @endforeach
                    </div>

                    @error('status')
                        <p style="margin:6px 0 0;font-size:13px;color:#e65100;">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Divider --}}
                <div style="height:1px;background:linear-gradient(90deg,#e5e7eb,transparent);margin-bottom:28px;"></div>

                {{-- Actions --}}
                <div style="display: flex; gap: 12px;">
                    <a href="{{ route('zones.index') }}" style="
                        flex: 1; padding: 14px; border-radius: 10px; text-align: center;
                        font-size: 15px; font-weight: 600; text-decoration: none;
                        background: #f5f7fa; color: #667085; border: 1.5px solid #e5e7eb;
                        display: flex; align-items: center; justify-content: center; gap: 8px;
                        transition: all 0.2s;
                    " onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f5f7fa'">
                        <i class="fas fa-times"></i> Annuler
                    </a>

                    <button type="submit" style="
                        flex: 2; padding: 14px; border-radius: 10px; border: none;
                        font-size: 15px; font-weight: 600; cursor: pointer;
                        background: linear-gradient(135deg,#00d084,#00a86b); color: white;
                        display: flex; align-items: center; justify-content: center; gap: 8px;
                        box-shadow: 0 4px 15px rgba(0,208,132,0.3); transition: all 0.3s;
                    " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0,208,132,0.4)'"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,208,132,0.3)'">
                        <i class="fas fa-plus-circle"></i> Créer la Zone
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection