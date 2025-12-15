using AuthService.Application.Dtos;
using AuthService.Application.Services;
using AuthService.Domain.Repositories;
using AuthService.Domain.Services;
using MediatR;

namespace AuthService.Application.Commands.Login;

public record LoginCommand(string Email, string Password)  : IRequest<AuthResult>;

public class LoginCommandHandler(IUserRepository userRepository, IPasswordService passwordService, IJwtService jwtService ) : IRequestHandler<LoginCommand, AuthResult>
{
    public async Task<AuthResult> Handle(LoginCommand request, CancellationToken cancellationToken)
    {
        var user = await userRepository.FindByEmailAsync(request.Email);
        if (user == null)
            throw new ApplicationException("Invalid credentials.");
        
        var isPasswordValid = passwordService.VerifyPasswordHash(
            request.Password, 
            user.Password
        );
        
        if (!isPasswordValid)
            throw new ApplicationException("Invalid credentials.");
        
        user.UpdateLastLogin();
        await userRepository.UpdateAsync(user);
        
        var token = jwtService.GenerateToken(user.Id, user.Email);
        
        return new AuthResult(user.Id, user.Email, token);
    }
}