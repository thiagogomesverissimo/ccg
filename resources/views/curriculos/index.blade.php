@extends('adminlte::page')

@section('title', config('app.name') . ' - Currículos')

@section('content_header')
    <h1>Currículos</h1>
@stop

@section('content')

    @include('flash')

    <div class="box box-primary">
        <div class="box-body">
            <table class="table table-bordered table-striped table-hover datatable">
                <thead>
                    <tr>
                        <th colspan="4">
                            <button type="button" class="btn btn-success btn-sm" title="Adicionar Currículo" onclick="location.href='curriculos/create';">
                                <span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;&nbsp;Adicionar Currículo
                            </button>                             
                        </th>
                    </tr>  
                    <tr>
                        <th colspan="4">
                            Listando Currículos &nbsp;&nbsp;&nbsp;<span class="badge bg-yellow">{{ substr($ano, 0, 4) }}</span>&nbsp;&nbsp;
                            @foreach ($anos as $a)
                                @if (substr($ano, 0, 4) != substr($a->dtainicrl, 0, 4))
                                    | <a href="/curriculos?ano={{ substr($a->dtainicrl, 0, 4) }}" 
                                        title="Listar currículos no ano de {{ substr($a->dtainicrl, 0, 4) }}">
                                        {{ substr($a->dtainicrl , 0, 4) }}</a>
                                @endif
                            @endforeach
                            | <a href="/curriculos?ano=Tudo" title="Lista todos os currículos">
                                Tudo</a> (pode demorar a carregar a página)
                        </th>
                    </tr>                   
                    <tr>
                        <th>Curso</th>
                        <th>Habilitação</th>
                        <th>Ingresso</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                
                @foreach ($curriculos as $curriculo)

                    <tr>
                        <td>{{ $curriculo->codcur }} - {{ Uspdev\Replicado\Graduacao::nomeCurso($curriculo->codcur) }}</td>
                        <td>{{ $curriculo->codhab }} - {{ Uspdev\Replicado\Graduacao::nomeHabilitacao($curriculo->codhab, $curriculo->codcur) }}</td>
                        <td>{{ Carbon\Carbon::parse($curriculo->dtainicrl)->format('Y') }}</td>
                        <td>
                            {{-- Se não existe disciplina cadastrada, pode apagar --}}
                            @if (
                                (App\DisciplinasObrigatoria::where('id_crl', $curriculo->id)->get()->count() == 0) and 
                                (App\DisciplinasOptativasEletiva::where('id_crl', $curriculo->id)->get()->count() == 0) and  
                                (App\DisciplinasLicenciatura::where('id_crl', $curriculo->id)->get()->count() == 0)                                 
                            ) 
                                <form role="form" method="POST" action="/curriculos/{{ $curriculo->id }}">
                                {{ csrf_field() }}
                                {{ method_field('delete') }}                            
                            @endif                                
                                <button type="button" class="btn btn-info btn-xs" 
                                    onclick='location.href="curriculos/{{ $curriculo->id }}";' title="Ver Currículo">
                                    <span class="glyphicon glyphicon-eye-open"></span>
                                </button>
                                <button type="button" class="btn btn-warning btn-xs" 
                                    onclick='window.open("curriculos/{{ $curriculo->id }}/print");' title="Versão de impressão do Currículo">
                                    <span class="glyphicon glyphicon-print"></span>
                                </button>                                
                                <button type="button" class="btn btn-primary btn-xs" 
                                    onclick='location.href="curriculos/{{ $curriculo->id }}/edit";' title="Editar Currículo">
                                    <span class="glyphicon glyphicon-pencil"></span>
                                </button>     
                                <button type="button" class="btn btn-primary btn-xs" 
                                    onclick='location.href="curriculos/{{ $curriculo->id }}/copy";' title="Copiar Currículo">
                                    <span class="glyphicon glyphicon-duplicate"></span>
                                </button>                                                        
                                <button type="button" class="btn btn-success btn-xs" title="Analisar Currículo">
                                    <span class="glyphicon glyphicon-ok"></span>
                                </button>
                            {{-- Se não existe disciplina cadastrada, pode apagar --}}
                            @if (
                                (App\DisciplinasObrigatoria::where('id_crl', $curriculo->id)->get()->count() == 0) and 
                                (App\DisciplinasOptativasEletiva::where('id_crl', $curriculo->id)->get()->count() == 0) and  
                                (App\DisciplinasLicenciatura::where('id_crl', $curriculo->id)->get()->count() == 0)                                 
                            )                             
                                <button type="button" class="btn btn-info btn-xs" 
                                    onclick='location.href="curriculos/{{ $curriculo->id }}/alunos";' title="Listar Alunos">
                                    <span class="glyphicon glyphicon-user"></span>
                                </button>                                
                                <button type="submit" class="btn btn-danger btn-xs" title="Apagar Currículo">
                                    <span class="glyphicon glyphicon-trash"></span>
                                </button>
                                </form>                                 
                            {{-- Se não, exibe modal avisando --}}
                            @else
                                <button type="button" class="btn btn-info btn-xs" 
                                    onclick='location.href="curriculos/{{ $curriculo->id }}/alunos";' title="Listar Alunos">
                                    <span class="glyphicon glyphicon-user"></span>
                                </button>                             
                                <button type="button" class="btn btn-danger btn-xs" title="Apagar Currículo" 
                                    data-toggle="modal" data-target="#diciplinas{{ $curriculo->id }}">
                                    <span class="glyphicon glyphicon-trash"></span>
                                </button> 
                                {{-- Modais com as disciplinas do currículo --}}
                                <div class="modal modal-danger fade" id="diciplinas{{ $curriculo->id }}">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title">Este currículo possui Disciplinas cadastradas!</h4>
                                            </div>
                                            <div class="modal-body">
                                                <p>
                                                    Curso: {{ $curriculo->codcur }} - {{ Uspdev\Replicado\Graduacao::nomeCurso($curriculo->codcur) }}<br />
                                                    Habilitação: {{ $curriculo->codhab }} - {{ Uspdev\Replicado\Graduacao::nomeHabilitacao($curriculo->codhab, $curriculo->codcur) }}<br />
                                                    Ingresso: {{ Carbon\Carbon::parse($curriculo->dtainicrl)->format('Y') }}
                                                </p>
                                                <p><strong>As Diciplinas abaixo serão automaticamente removidas junto com o Currículo</strong></p>
                                                <p><strong>Obrigatórias</strong>
                                                @foreach (App\DisciplinasObrigatoria::where('id_crl', $curriculo->id)->get() as $obrigatoria)
                                                    <br />{{ $obrigatoria['coddis'] }} - 
                                                        {{ Uspdev\Replicado\Graduacao::nomeDisciplina($obrigatoria['coddis']) }}
                                                @endforeach
                                                </p>
                                                <p><strong>Optativas Eletivas</strong>
                                                @foreach (App\DisciplinasOptativasEletiva::where('id_crl', $curriculo->id)->get() as $optativasEletiva)
                                                    <br />{{ $optativasEletiva['coddis'] }} - 
                                                        {{ Uspdev\Replicado\Graduacao::nomeDisciplina($optativasEletiva['coddis']) }}
                                                @endforeach
                                                </p>
                                                <p><strong>Licenciaturas</strong>
                                                @foreach (App\DisciplinasLicenciatura::where('id_crl', $curriculo->id)->get() as $licenciatura)
                                                    <br />{{ $licenciatura['coddis'] }} - 
                                                        {{ Uspdev\Replicado\Graduacao::nomeDisciplina($licenciatura['coddis']) }}
                                                @endforeach
                                                </p>                                                                                                  
                                            </div>
                                            <form role="form" method="POST" action="curriculos/{{ $curriculo->id }}">
                                                {{ csrf_field() }}
                                                {{ method_field('delete') }} 
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-outline">Apagar</button>
                                            </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>
                
                @endforeach
                
                </tbody>
            </table>
        </div>  
    </div>

@stop

@section('js')
    
    <script type="text/javascript">
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2({
                placeholder:    "Selecione",
                allowClear:     true
            });
            
            //Datepicker
            $('.datepicker').datepicker({
                format:         "dd/mm/yyyy",
                viewMode:       "years", 
                minViewMode:    "years",
                autoclose:      true
            });

            // DataTables
            $('.datatable').DataTable({
                language    	: {
                    url     : '//cdn.datatables.net/plug-ins/1.10.19/i18n/Portuguese-Brasil.json'
                },  
                paging      	: true,
                lengthChange	: true,
                searching   	: true,
                ordering    	: true,
                info        	: true,
                autoWidth   	: true,
                lengthMenu		: [
					[ 10, 25, 50, 100, -1 ],
					[ '10 linhas', '25 linhas', '50 linhas', '100 linhas', 'Mostar todos' ]
    			],
				pageLength  	: -1
            });
        })
    </script>

@stop
