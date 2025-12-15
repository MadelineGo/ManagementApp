using MediatR;

namespace AuthService.Application.Commands.Logout;

public class LogoutCommandHandler : IRequestHandler<LogoutCommand, bool>
{
    public Task<bool> Handle(LogoutCommand request, CancellationToken cancellationToken)
    {
        // Since we are using stateless JWTs without a blacklist, there is no server-side action needed.
        // In a stateful scenario, we would invalidate the session or revoke the token here.
        return Task.FromResult(true);
    }
}
