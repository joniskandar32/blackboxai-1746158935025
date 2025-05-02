import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  private baseUrl = 'http://localhost/hris-backend'; // Adjust backend URL as needed

  constructor(private http: HttpClient) { }

  // Authentication
  login(username: string, password: string): Observable<any> {
    return this.http.post(`${this.baseUrl}/auth`, { username, password });
  }

  // Employees
  getEmployees(): Observable<any> {
    return this.http.get(`${this.baseUrl}/employees`);
  }

  getEmployee(id: number): Observable<any> {
    return this.http.get(`${this.baseUrl}/employees/${id}`);
  }

  createEmployee(employee: any): Observable<any> {
    return this.http.post(`${this.baseUrl}/employees`, employee);
  }

  updateEmployee(id: number, employee: any): Observable<any> {
    return this.http.put(`${this.baseUrl}/employees/${id}`, employee);
  }

  deleteEmployee(id: number): Observable<any> {
    return this.http.delete(`${this.baseUrl}/employees/${id}`);
  }

  // Attendance
  getAttendance(): Observable<any> {
    return this.http.get(`${this.baseUrl}/attendance`);
  }

  recordAttendance(attendance: any): Observable<any> {
    return this.http.post(`${this.baseUrl}/attendance`, attendance);
  }
}
