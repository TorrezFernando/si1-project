@extends('adminlte::page')

@section('title', 'Horario')

@section('content_header')
    <h1>{{ $user->id_rol == 1 ? 'Supervisar Horarios' : 'Mi Horario' }}</h1>
@stop

@section('content')
    @php
        $colores = [
            'Lunes'     => ['bg' => '#2563eb', 'light' => '#eff6ff', 'gradient' => 'linear-gradient(135deg, #2563eb, #3b82f6)'],
            'Martes'    => ['bg' => '#7c3aed', 'light' => '#f5f3ff', 'gradient' => 'linear-gradient(135deg, #7c3aed, #a78bfa)'],
            'Miércoles' => ['bg' => '#db2777', 'light' => '#fdf2f8', 'gradient' => 'linear-gradient(135deg, #db2777, #f472b6)'],
            'Jueves'    => ['bg' => '#ea580c', 'light' => '#fff7ed', 'gradient' => 'linear-gradient(135deg, #ea580c, #fb923c)'],
            'Viernes'   => ['bg' => '#16a34a', 'light' => '#f0fdf4', 'gradient' => 'linear-gradient(135deg, #16a34a, #4ade80)'],
        ];
        $totalClases = collect($horariosPorDia)->flatten()->count();
    @endphp

    {{-- Admin: professor switcher --}}
    @if($user->id_rol == 1)
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-header" style="border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                <span style="font-weight: 700; color: #1e293b; font-size: 0.9rem;">
                    <i class="fas fa-users mr-1" style="color: #3b82f6;"></i>
                    <span id="professorCount">{{ count($profesores) }} profesores</span>
                </span>
                <button type="button" class="btn btn-sm"
                        style="border: 1.5px solid #dbeafe; border-radius: 10px; color: #2563eb; font-weight: 600;"
                        onclick="document.getElementById('profPicker').classList.toggle('d-none')">
                    <i class="fas fa-exchange-alt mr-1"></i> Cambiar profesor
                </button>
            </div>
        </div>

        <div id="profPicker" class="d-none">
            <div class="card-body" style="border-bottom: 1px solid #e2e8f0;">
                <input type="text" id="profSearch" class="form-control"
                       placeholder="Buscar profesor por nombre..."
                       style="border-radius: 10px; margin-bottom: 0.8rem;"
                       oninput="filterProfessors(this.value)">
                <div id="profList" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 0.5rem; max-height: 280px; overflow-y: auto;">
                    @foreach($profesores as $p)
                        @php
                            $isActiveProf = $idProfesor == $p->id_profesor;
                            $fullName = trim($p->nombre . ' ' . $p->ap_paterno . ' ' . $p->ap_materno);
                            $initials = strtoupper(substr($p->nombre, 0, 1) . substr($p->ap_paterno, 0, 1));
                        @endphp
                        <a href="{{ route('profesor.horario', ['id_profesor' => $p->id_profesor]) }}"
                           class="text-decoration-none prof-item"
                           data-search="{{ strtolower($fullName) }}">
                            <div style="display: flex; align-items: center; gap: 0.6rem; padding: 0.55rem 0.8rem; border-radius: 10px;
                                        border: 1.5px solid {{ $isActiveProf ? '#3b82f6' : '#e2e8f0' }};
                                        background: {{ $isActiveProf ? '#eff6ff' : '#ffffff' }}; transition: all 0.2s; cursor: pointer;">
                                <div style="width: 32px; height: 32px; border-radius: 8px;
                                            background: {{ $isActiveProf ? 'linear-gradient(135deg, #2563eb, #3b82f6)' : '#e2e8f0' }};
                                            color: {{ $isActiveProf ? '#fff' : '#94a3b8' }};
                                            display: flex; align-items: center; justify-content: center;
                                            font-weight: 700; font-size: 0.8rem; flex-shrink: 0;">
                                    {{ $initials }}
                                </div>
                                <div style="min-width: 0; font-size: 0.85rem; font-weight: 600; color: #1e293b;
                                            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $fullName }}
                                </div>
                                @if($isActiveProf)
                                    <i class="fas fa-check-circle" style="color: #3b82f6; margin-left: auto;"></i>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        @if($profesor)
        <div class="card-body" style="padding: 0.7rem 1rem;">
            <span style="font-size: 0.82rem; color: #64748b; margin-right: 0.5rem;">Seleccionado:</span>
            <span class="badge-chip" style="font-size: 0.85rem; padding: 0.3rem 0.8rem; background: #eff6ff; color: #1e40af; font-weight: 600;">
                <i class="fas fa-chalkboard-teacher mr-1"></i>
                {{ trim($profesor->nombre . ' ' . $profesor->ap_paterno) }}
            </span>
        </div>
        @endif
    </div>
    @endif

    {{-- Professor info header --}}
    @if($profesor)
    <div class="welcome-header" style="margin-bottom: 1.5rem;">
        <span class="role-badge">Profesor</span>
        <div class="welcome-title">
            {{ trim($profesor->nombre . ' ' . $profesor->ap_paterno . ' ' . $profesor->ap_materno) }}
        </div>
        <div class="welcome-subtitle">
            <i class="fas fa-calendar-week mr-1"></i>
            {{ $totalClases }} {{ $totalClases == 1 ? 'clase' : 'clases' }} programadas esta semana
        </div>
    </div>
    @endif

    {{-- Weekly schedule grid --}}
    <div class="row">
        @foreach($dias as $dia)
        @php
            $count = $horariosPorDia[$dia]->count();
            $color = $colores[$dia];
            $isToday = strtolower(\Carbon\Carbon::now()->locale('es')->dayName) === strtolower($dia);
        @endphp
        <div class="col-12 col-md-6 col-xl mb-4">
            <div class="card h-100" style="border-radius: 14px; overflow: hidden; border: 1px solid #e2e8f0; {{ $isToday ? 'box-shadow: 0 4px 25px rgba(37,99,235,0.1);' : 'box-shadow: 0 2px 8px rgba(0,0,0,0.03);' }}">
                {{-- Day header --}}
                <div style="background: {{ $color['gradient'] }}; padding: 0.8rem 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span style="color: #fff; font-weight: 800; font-size: 0.95rem; letter-spacing: -0.01em;">
                        {{ $dia }}
                    </span>
                    @if($isToday)
                        <span style="background: rgba(255,255,255,0.25); color: #fff; padding: 0.1rem 0.5rem; border-radius: 50px; font-size: 0.65rem; font-weight: 700; text-transform: uppercase;">Hoy</span>
                    @endif
                    <span style="margin-left: auto; background: rgba(255,255,255,0.2); color: #fff; padding: 0.15rem 0.6rem; border-radius: 50px; font-size: 0.72rem; font-weight: 700;">
                        {{ $count }} {{ $count == 1 ? 'clase' : 'clases' }}
                    </span>
                </div>

                {{-- Day body --}}
                <div class="card-body" style="padding: 0.6rem;">
                    @if($count === 0)
                        <div style="text-align: center; padding: 1.5rem 0.5rem;">
                            <div style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.4;">☕</div>
                            <p style="color: #94a3b8; font-size: 0.85rem; margin: 0;">Sin clases</p>
                        </div>
                    @else
                        @foreach($horariosPorDia[$dia] as $h)
                        <div style="background: {{ $color['light'] }}; border-left: 4px solid {{ $color['bg'] }};
                                    border-radius: 8px; padding: 0.65rem 0.8rem; margin-bottom: 0.5rem;
                                    transition: all 0.2s; cursor: default;"
                             onmouseover="this.style.transform='translateX(3px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.06)';"
                             onmouseout="this.style.transform=''; this.style.boxShadow='';">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.3rem;">
                                <strong style="color: {{ $color['bg'] }}; font-size: 0.9rem;">
                                    {{ $h->materia }}
                                </strong>
                                <span style="background: {{ $color['bg'] }}; color: #fff; padding: 0.1rem 0.45rem; border-radius: 6px; font-size: 0.68rem; font-weight: 700; white-space: nowrap;">
                                    {{ $h->paralelo }}
                                </span>
                            </div>
                            <div style="display: flex; flex-wrap: wrap; gap: 0.4rem 1rem; font-size: 0.78rem; color: #64748b; margin-bottom: 0.2rem;">
                                <span><i class="fas fa-clock mr-1" style="font-size: 0.65rem;"></i> {{ substr($h->hora_inicio, 0, 5) }} – {{ substr($h->hora_fin, 0, 5) }}</span>
                                <span><i class="fas fa-graduation-cap mr-1" style="font-size: 0.65rem;"></i> {{ $h->curso }}</span>
                            </div>
                            <div style="font-size: 0.75rem; color: #94a3b8;">
                                <i class="fas fa-door-open mr-1" style="font-size: 0.6rem;"></i> {{ $h->aula }}
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Summary footer --}}
    <div class="card" style="margin-top: 0.5rem;">
        <div class="card-body" style="padding: 1rem 1.5rem;">
            <h6 style="font-weight: 700; color: #475569; margin-bottom: 1rem;">
                <i class="fas fa-chart-bar mr-2" style="color: #3b82f6;"></i>Resumen semanal
            </h6>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 0.6rem;">
                @foreach($dias as $dia)
                    <div style="text-align: center; padding: 0.6rem 0.4rem; border-radius: 10px; background: {{ $colores[$dia]['light'] }};">
                        <div style="font-size: 1.4rem; font-weight: 800; color: {{ $colores[$dia]['bg'] }};">
                            {{ $horariosPorDia[$dia]->count() }}
                        </div>
                        <div style="font-size: 0.7rem; color: #64748b; font-weight: 500;">{{ substr($dia, 0, 3) }}</div>
                    </div>
                @endforeach
                <div style="text-align: center; padding: 0.6rem 0.4rem; border-radius: 10px; background: #f1f5f9; border: 1.5px solid #e2e8f0;">
                    <div style="font-size: 1.4rem; font-weight: 800; color: #1e293b;">
                        {{ $totalClases }}
                    </div>
                    <div style="font-size: 0.7rem; color: #94a3b8; font-weight: 500;">Total</div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    function filterProfessors(query) {
        var items = document.querySelectorAll('.prof-item');
        var q = query.toLowerCase().trim();
        items.forEach(function(item) {
            var search = item.getAttribute('data-search') || '';
            item.style.display = !q || search.indexOf(q) !== -1 ? '' : 'none';
        });
    }
</script>
@stop
