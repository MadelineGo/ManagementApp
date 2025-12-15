namespace AuthService.Domain.Services;

public interface IJwtService
{
    string GenerateToken(int userId, string email);
    
}