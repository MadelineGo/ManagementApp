namespace AuthService.Application.Dtos;

public record AuthResult(int UserId, string Email, string Token);