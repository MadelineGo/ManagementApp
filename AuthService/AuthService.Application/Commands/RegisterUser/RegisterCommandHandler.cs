using AuthService.Application.Dtos;
using AuthService.Application.Services;
using AuthService.Domain.Models;
using AuthService.Domain.Repositories;
using AuthService.Domain.Services;
using MediatR;

namespace AuthService.Application.Commands.Register;

public record RegisterCommand(string Name, string LastName, string Email, string Password) : IRequest<AuthResult>;

public class RegisterCommandHandler(IUserRepository userRepository, IPasswordService  passwordService, IJwtService  jwtService ) : IRequestHandler<RegisterCommand, AuthResult>
{
    public async Task<AuthResult> Handle(RegisterCommand request, CancellationToken cancellationToken)
    {
        var existingUser = await userRepository.FindByEmailAsync(request.Email);
        if (existingUser != null)
            throw new ApplicationException("User with this email already exists.");
        
        var hash = passwordService.CreatePasswordHash(request.Password);
        
        var newUser = User.Create(request.Name, request.LastName, request.Email, hash);
        await userRepository.AddAsync(newUser);
        var token = jwtService.GenerateToken(newUser.Id, newUser.Email);
        return new AuthResult(newUser.Id, newUser.Email, token);
    }
}