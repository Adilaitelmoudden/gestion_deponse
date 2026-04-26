@extends('layouts.app')

@section('title', 'Rapports')
@section('header', 'Générer des Rapports')

@section('content')
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-bar"></i> Générer un Rapport</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('reports.generate') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="report_type" class="form-label">Type de Rapport *</label>
                        <select class="form-control @error('report_type') is-invalid @enderror" 
                                id="report_type" name="report_type" required>
                            <option value="">Sélectionner...</option>
                            <option value="monthly">Rapport Mensuel</option>
                            <option value="yearly">Rapport Annuel</option>
                            <option value="category">Rapport par Catégorie</option>
                        </select>
                        @error('report_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3" id="month_field" style="display: none;">
                        <label for="month" class="form-label">Mois</label>
                        <select class="form-control" id="month" name="month">
                            <option value="1">Janvier</option>
                            <option value="2">Février</option>
                            <option value="3">Mars</option>
                            <option value="4">Avril</option>
                            <option value="5">Mai</option>
                            <option value="6">Juin</option>
                            <option value="7">Juillet</option>
                            <option value="8">Août</option>
                            <option value="9">Septembre</option>
                            <option value="10">Octobre</option>
                            <option value="11">Novembre</option>
                            <option value="12">Décembre</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="year" class="form-label">Année *</label>
                        <select class="form-control @error('year') is-invalid @enderror" 
                                id="year" name="year" required>
                            @foreach($years as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                        @error('year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download"></i> Générer le Rapport
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('report_type').addEventListener('change', function() {
    const monthField = document.getElementById('month_field');
    if(this.value === 'monthly') {
        monthField.style.display = 'block';
    } else {
        monthField.style.display = 'none';
    }
});
</script>
@endsection