import { bootstrapApplication } from '@angular/platform-browser';
import { provideAnimations } from '@angular/platform-browser/animations';
import { provideHttpClient } from '@angular/common/http';
import { provideRouter, Routes } from '@angular/router';
import { AppComponent } from './app/app.component';
import { DashboardPageComponent } from './app/features/dashboard/pages/dashboard-page.component';

const routes: Routes = [{ path: '', component: DashboardPageComponent }];

bootstrapApplication(AppComponent, {
  providers: [provideAnimations(), provideHttpClient(), provideRouter(routes)],
});
