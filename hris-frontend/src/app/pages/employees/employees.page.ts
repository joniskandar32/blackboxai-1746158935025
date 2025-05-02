import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ApiService } from '../../services/api.service';

@Component({
  selector: 'app-employees',
  templateUrl: './employees.page.html',
  styleUrls: ['./employees.page.scss'],
})
export class EmployeesPage implements OnInit {
  employees: any[] = [];

  constructor(private apiService: ApiService, private router: Router) {}

  ngOnInit() {
    this.loadEmployees();
  }

  loadEmployees() {
    this.apiService.getEmployees().subscribe({
      next: (data) => {
        this.employees = data;
      },
      error: (err) => {
        console.error('Failed to load employees', err);
      },
    });
  }

  viewEmployee(id: number) {
    this.router.navigate(['/employee', id]);
  }

  addEmployee() {
    this.router.navigate(['/employee', 'new']);
  }
}
