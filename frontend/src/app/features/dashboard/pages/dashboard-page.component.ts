import { Component, OnInit, ViewChild } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormControl, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { BaseChartDirective } from 'ng2-charts';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatTableDataSource, MatTableModule } from '@angular/material/table';
import { MatPaginator, MatPaginatorModule } from '@angular/material/paginator';
import { MatSort, MatSortModule } from '@angular/material/sort';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatSidenavModule } from '@angular/material/sidenav';
import { MatListModule } from '@angular/material/list';
import { ApiService } from '../../../core/services/api.service';
import { Kpis, RaceResultRow, HorseDetail } from '../../../models/dashboard.models';

@Component({
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, BaseChartDirective, MatCardModule, MatFormFieldModule, MatInputModule, MatButtonModule, MatTableModule, MatPaginatorModule, MatSortModule, MatProgressSpinnerModule, MatSidenavModule, MatListModule],
  templateUrl: './dashboard-page.component.html',
  styleUrl: './dashboard-page.component.scss',
})
export class DashboardPageComponent implements OnInit {
  @ViewChild(MatPaginator) paginator!: MatPaginator;
  @ViewChild(MatSort) sort!: MatSort;
  @ViewChild(BaseChartDirective) chart?: BaseChartDirective;

  filtersForm = new FormGroup({ startDate: new FormControl(''), endDate: new FormControl(''), racecourse: new FormControl(''), discipline: new FormControl('') });
  kpis?: Kpis;
  loading = false;
  error = '';
  displayedColumns = ['date','racecourse','race','horse','jockeyOrDriver','trainer','distance','groundCondition','odds','finishPosition','earnings','roiSimulated'];
  dataSource = new MatTableDataSource<RaceResultRow>([]);
  horseDetail?: HorseDetail;
  chartData: any = { labels: [], datasets: [{ data: [], label: 'Victoires' }, { data: [], label: 'Places' }] };
  racecourseChartData: any = { labels: [], datasets: [{ data: [], label: 'Win rate' }] };
  distanceChartData: any = { labels: [], datasets: [{ data: [], label: 'Gains moyens' }] };
  oddsScatterData: any = { datasets: [{ data: [], label: 'Cote vs Rang' }] };
  jockeyStats: any[] = [];
  trainerStats: any[] = [];

  constructor(private api: ApiService) {}

  ngOnInit(): void { this.loadAll(); }

  applyFilters(): void { this.loadAll(); }

  loadAll(): void {
    this.loading = true;
    const filters = this.cleanFilters();
    this.api.kpis(filters).subscribe({ next: (x)=> this.kpis = x });
    this.api.raceResults(filters).subscribe({
      next: (rows) => { this.dataSource.data = rows; this.dataSource.paginator = this.paginator; this.dataSource.sort = this.sort; this.loading = false; },
      error: () => { this.error = 'Erreur lors du chargement des résultats.'; this.loading = false; }
    });
    this.api.perfOverTime(filters).subscribe(d => this.chartData = { labels: d.map(x=>x.date), datasets: [{ data: d.map(x=>x.wins), label: 'Victoires' }, { data: d.map(x=>x.places), label: 'Places' }] });
    this.api.byRacecourse(filters).subscribe(d => this.racecourseChartData = { labels: d.map(x=>x.label), datasets: [{ data: d.map(x=>x.winRate), label: 'Win rate' }] });
    this.api.byDistance(filters).subscribe(d => this.distanceChartData = { labels: d.map(x=>x.label), datasets: [{ data: d.map(x=>x.averageEarnings), label: 'Gains moyens' }] });
    this.api.oddsVsResults(filters).subscribe(d => this.oddsScatterData = { datasets: [{ data: d.map(x=>({x:x.odds, y:x.finishPosition})), label: 'Cote vs Résultat' }] });
    this.api.jockeyStats(filters).subscribe(d=>this.jockeyStats=d);
    this.api.trainerStats(filters).subscribe(d=>this.trainerStats=d);
  }

  onRowClick(row: RaceResultRow): void {
    const id = Number((this.dataSource.data.find(x=>x.horse===row.horse) as any)?.id ?? 0);
    if (!id) return;
    this.api.horseDetail(id).subscribe(d => this.horseDetail = d);
  }

  search(term: string): void { this.dataSource.filter = term.trim().toLowerCase(); }

  private cleanFilters(): Record<string, string> {
    return Object.fromEntries(Object.entries(this.filtersForm.value).map(([k,v])=>[k,String(v ?? '')]));
  }
}
