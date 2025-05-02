import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { ApiService } from '../../services/api.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss'],
})
export class LoginPage {
  username = '';
  password = '';
  loading = false;
  errorMessage = '';

  constructor(private apiService: ApiService, private router: Router) {}

  onLogin() {
    this.loading = true;
    this.errorMessage = '';
    this.apiService.login(this.username, this.password).subscribe({
      next: (res) => {
        this.loading = false;
        // For simplicity, store user info in localStorage
        localStorage.setItem('user', JSON.stringify(res.user));
        this.router.navigate(['/employees']);
      },
      error: (err) => {
        this.loading = false;
        this.errorMessage = err.error?.error || 'Login failed';
      },
    });
  }
}
