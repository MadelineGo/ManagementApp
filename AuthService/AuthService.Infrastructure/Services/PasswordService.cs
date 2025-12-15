using AuthService.Application.Services;

namespace AuthService.Infrastructure.Services;

public class PasswordService : IPasswordService
{
    public string CreatePasswordHash(string password)
    {
        return BCrypt.Net.BCrypt.HashPassword(password, workFactor: 12);
    }

    public bool VerifyPasswordHash(string password, string hash)
    {
        return BCrypt.Net.BCrypt.Verify(password, hash);
    }
}