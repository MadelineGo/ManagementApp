using MediatR;

namespace AuthService.Application.Commands.Logout;

public record LogoutCommand : IRequest<bool>;
