namespace AuthService.Application.Services;

public interface IPasswordService
{
    string CreatePasswordHash(string password);
    bool VerifyPasswordHash(string password, string storedHash);
}