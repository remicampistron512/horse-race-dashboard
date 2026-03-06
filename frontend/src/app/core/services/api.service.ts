import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { HorseDetail, Kpis, RaceResultRow } from '../../models/dashboard.models';

@Injectable({ providedIn: 'root' })
export class ApiService {
  private readonly base = 'http://127.0.0.1:8000/api';
  constructor(private http: HttpClient) {}

  kpis(filters: Record<string, string> = {}): Observable<Kpis> {
    return this.http.get<Kpis>(`${this.base}/dashboard/kpis`, { params: this.toParams(filters) });
  }
  perfOverTime(filters: Record<string, string> = {}): Observable<any[]> {
    return this.http.get<any[]>(`${this.base}/dashboard/performance-over-time`, { params: this.toParams(filters) });
  }
  byRacecourse(filters: Record<string, string> = {}): Observable<any[]> {
    return this.http.get<any[]>(`${this.base}/dashboard/by-racecourse`, { params: this.toParams(filters) });
  }
  byDistance(filters: Record<string, string> = {}): Observable<any[]> {
    return this.http.get<any[]>(`${this.base}/dashboard/by-distance`, { params: this.toParams(filters) });
  }
  heatmap(filters: Record<string, string> = {}): Observable<any[]> {
    return this.http.get<any[]>(`${this.base}/dashboard/heatmap`, { params: this.toParams(filters) });
  }
  oddsVsResults(filters: Record<string, string> = {}): Observable<any[]> {
    return this.http.get<any[]>(`${this.base}/dashboard/odds-vs-results`, { params: this.toParams(filters) });
  }
  raceResults(filters: Record<string, string> = {}): Observable<RaceResultRow[]> {
    return this.http.get<RaceResultRow[]>(`${this.base}/race-results`, { params: this.toParams(filters) });
  }
  horses(): Observable<any[]> { return this.http.get<any[]>(`${this.base}/horses`); }
  horseDetail(id: number): Observable<HorseDetail> { return this.http.get<HorseDetail>(`${this.base}/horses/${id}`); }
  jockeyStats(filters: Record<string, string> = {}): Observable<any[]> { return this.http.get<any[]>(`${this.base}/jockeys-drivers/stats`, { params: this.toParams(filters) }); }
  trainerStats(filters: Record<string, string> = {}): Observable<any[]> { return this.http.get<any[]>(`${this.base}/trainers/stats`, { params: this.toParams(filters) }); }

  private toParams(filters: Record<string, string>): HttpParams { let p = new HttpParams(); Object.entries(filters).forEach(([k,v])=>{ if(v) p = p.set(k,v);}); return p; }
}
