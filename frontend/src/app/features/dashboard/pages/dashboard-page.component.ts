import { Component, OnInit, ViewChild } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { ChartConfiguration } from 'chart.js';
import { BaseChartDirective } from 'ng2-charts';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatTableDataSource, MatTableModule } from '@angular/material/table';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatChipsModule } from '@angular/material/chips';
import { ApiService } from '../../../core/services/api.service';
import { RaceResult, RaceSearchFilters } from '../../../models/dashboard.models';

interface DashboardFilters {
  date: FormControl<string | null>;
  hippo: FormControl<string | null>;
  prix: FormControl<string | null>;
}

interface KpiCard {
  label: string;
  value: string;
  supportingText: string;
  icon: string;
}

@Component({
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    BaseChartDirective,
    MatCardModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatIconModule,
    MatTableModule,
    MatProgressSpinnerModule,
    MatChipsModule,
  ],
  templateUrl: './dashboard-page.component.html',
  styleUrl: './dashboard-page.component.scss',
})
export class DashboardPageComponent implements OnInit {
  @ViewChild(BaseChartDirective) chart?: BaseChartDirective;

  readonly filtersForm = new FormGroup<DashboardFilters>({
    date: new FormControl<string | null>(null),
    hippo: new FormControl<string | null>(null),
    prix: new FormControl<string | null>(null),
  });

  readonly displayedColumns = [
    'date',
    'lieu',
    'type',
    'distance',
    'montant',
    'pix',
    'r/c',
    'partants',
    'arrivee',
  ];

  readonly dataSource = new MatTableDataSource<RaceResult>([]);

  readonly raceByHippoChartData: ChartConfiguration<'bar'>['data'] = {
    labels: [],
    datasets: [
      {
        label: 'Nombre de résultats',
        data: [],
        borderRadius: 6,
        maxBarThickness: 34,
        backgroundColor: '#3f51b5cc',
        hoverBackgroundColor: '#3446af',
      },
    ],
  };

  readonly raceByHippoChartOptions: ChartConfiguration<'bar'>['options'] = {
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: false,
      },
    },
    scales: {
      x: {
        grid: {
          display: false,
        },
        ticks: {
          color: '#5f6b82',
          font: {
            family: 'Inter, Roboto, Arial, sans-serif',
          },
        },
      },
      y: {
        beginAtZero: true,
        ticks: {
          color: '#5f6b82',
          precision: 0,
          font: {
            family: 'Inter, Roboto, Arial, sans-serif',
          },
        },
        grid: {
          color: '#edf1f7',
        },
      },
    },
  };

  loading = false;
  errorMessage = '';
  selectedResult?: RaceResult;

  ngOnInit(): void {
    this.loadResults();
  }

  constructor(private api: ApiService) {}

  get kpiCards(): KpiCard[] {
    const results = this.dataSource.data;
    const totalResults = results.length;
    const hippodromes = new Set(results.map((item) => item.lieu || 'Inconnu')).size;

    const distanceValues = results
      .map((item) => this.parseNumber(item.distance))
      .filter((value): value is number => value !== null);

    const montantValues = results
      .map((item) => this.parseNumber(item.montant))
      .filter((value): value is number => value !== null);

    const averageDistance =
      distanceValues.length > 0
        ? Math.round(distanceValues.reduce((sum, value) => sum + value, 0) / distanceValues.length)
        : null;

    const averageMontant =
      montantValues.length > 0
        ? Math.round(montantValues.reduce((sum, value) => sum + value, 0) / montantValues.length)
        : null;

    return [
      {
        label: 'Total résultats',
        value: totalResults.toLocaleString('fr-FR'),
        supportingText: 'Courses correspondant aux filtres',
        icon: 'query_stats',
      },
      {
        label: 'Hippodromes actifs',
        value: hippodromes.toLocaleString('fr-FR'),
        supportingText: 'Lieux présents dans la sélection',
        icon: 'location_city',
      },
      {
        label: 'Distance moyenne',
        value: averageDistance !== null ? `${averageDistance.toLocaleString('fr-FR')} m` : '—',
        supportingText: 'Calculée sur les courses disponibles',
        icon: 'straighten',
      },
      {
        label: 'Montant moyen',
        value: averageMontant !== null ? `${averageMontant.toLocaleString('fr-FR')} €` : '—',
        supportingText: 'Allocation moyenne observée',
        icon: 'payments',
      },
    ];
  }

  search(): void {
    this.loadResults();
  }

  reset(): void {
    this.filtersForm.reset({ date: null, hippo: null, prix: null });
    this.selectedResult = undefined;
    this.loadResults();
  }

  selectResult(row: RaceResult): void {
    this.selectedResult = row;
  }

  trackByKpiLabel(_: number, card: KpiCard): string {
    return card.label;
  }

  private loadResults(): void {
    this.loading = true;
    this.errorMessage = '';

    this.api.searchRaceResults(this.buildFilters()).subscribe({
      next: (results) => {
        this.dataSource.data = results;
        this.selectedResult = results[0];
        this.updateCharts(results);
        this.loading = false;
      },
      error: (error: Error) => {
        this.dataSource.data = [];
        this.selectedResult = undefined;
        this.updateCharts([]);
        this.errorMessage = error.message;
        this.loading = false;
      },
    });
  }

  private buildFilters(): RaceSearchFilters {
    const dateValue = this.filtersForm.controls.date.value;

    return {
      date: dateValue ? this.toApiDate(dateValue) : undefined,
      hippo: this.filtersForm.controls.hippo.value ?? undefined,
      prix: this.filtersForm.controls.prix.value ?? undefined,
    };
  }

  private toApiDate(dateValue: string): string {
    const [year, month, day] = dateValue.split('-');
    if (!day || !month || !year) {
      return dateValue;
    }

    return `${day}/${month}/${year}`;
  }

  private parseNumber(value: string): number | null {
    if (!value) {
      return null;
    }

    const normalized = value.replace(/\s/g, '').replace(',', '.').match(/\d+(\.\d+)?/);
    if (!normalized) {
      return null;
    }

    const parsed = Number(normalized[0]);
    return Number.isFinite(parsed) ? parsed : null;
  }

  private updateCharts(results: RaceResult[]): void {
    const countsByHippo = results.reduce<Record<string, number>>((acc, item) => {
      const key = item.lieu || 'Inconnu';
      acc[key] = (acc[key] ?? 0) + 1;
      return acc;
    }, {});

    this.raceByHippoChartData.labels = Object.keys(countsByHippo);
    this.raceByHippoChartData.datasets[0].data = Object.values(countsByHippo);
    this.chart?.update();
  }
}
