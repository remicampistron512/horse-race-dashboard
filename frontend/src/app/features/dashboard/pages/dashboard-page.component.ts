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
import { ApiService } from '../../../core/services/api.service';
import { RaceResult, RaceSearchFilters } from '../../../models/dashboard.models';

interface DashboardFilters {
  date: FormControl<string | null>;
  hippo: FormControl<string | null>;
  prix: FormControl<string | null>;
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
      },
    ],
  };

  loading = false;
  errorMessage = '';
  selectedResult?: RaceResult;

  ngOnInit(): void {
    this.loadResults();
  }

  constructor(private api: ApiService) {}

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
