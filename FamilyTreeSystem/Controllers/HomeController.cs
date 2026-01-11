using System.Diagnostics;
using Microsoft.AspNetCore.Mvc;
using FamilyTreeSystem.Data;
using FamilyTreeSystem.Models;
using Microsoft.EntityFrameworkCore;

namespace FamilyTreeSystem.Controllers;

public class HomeController : Controller
{
    private readonly ILogger<HomeController> _logger;
    private readonly FamilyTreeContext _context;

    public HomeController(ILogger<HomeController> logger, FamilyTreeContext context)
    {
        _logger = logger;
        _context = context;
    }

    public IActionResult Index()
    {
        // 获取系统统计数据
        ViewBag.TotalFamilyMembers = _context.FamilyMembers.Count();
        ViewBag.TotalZiwei = _context.Ziwei.Count();
        ViewBag.TotalFamilyRules = _context.FamilyRules.Count();
        ViewBag.TotalClanActivities = _context.ClanActivities.Count();
        ViewBag.TotalClans = _context.Clans.Count();

        
        // 获取所有家族成员数据，使用AsNoTracking避免跟踪冲突
        var allMembers = _context.FamilyMembers.Include(m => m.Spouse).AsNoTracking().ToList();
        
        // 构建家族树结构
        var familyTreeData = BuildFamilyTree(allMembers);
        
        // 添加调试信息
        Console.WriteLine($"Total family members: {allMembers.Count}");
        Console.WriteLine($"Family tree data count: {familyTreeData.Count}");
        
        // 将家族树数据传递到视图
        ViewBag.FamilyTreeData = System.Text.Json.JsonSerializer.Serialize(familyTreeData);
        
        // 获取最新动态数据
        var latestNews = GetLatestNews();
        ViewBag.LatestNews = latestNews;
        
        return View();
    }
    
    private List<object> BuildFamilyTree(List<FamilyMember> allMembers)
    {
        // 创建一个字典以便快速查找成员
        var memberDict = allMembers.ToDictionary(m => m.Id);
        
        // 找到所有成员（简化为所有成员，不考虑父母关系）
        var potentialRoots = allMembers.ToList();
        
        // 筛选出男性成员
        var maleRoots = potentialRoots.Where(m => m.Gender == "Male").ToList();
        
        // 递归构建家族树
        var familyTree = new List<object>();
        
        // 如果有男性根节点，选择最早的一代（Generation最小）的男性作为唯一始祖
        if (maleRoots.Any())
        {
            // 按Generation升序排序，选择最早的一代（处理Generation为null的情况）
            var maleRootsWithGeneration = maleRoots.Where(m => m.Generation.HasValue).ToList();
            
            if (maleRootsWithGeneration.Any())
            {
                var earliestGeneration = maleRootsWithGeneration.Min(m => m.Generation);
                var earliestMaleRoots = maleRoots.Where(m => m.Generation == earliestGeneration).ToList();
                
                // 如果同一代有多个男性根节点，选择其中一个（按ID排序取第一个）
                var uniqueRoot = earliestMaleRoots.OrderBy(m => m.Id).FirstOrDefault();
                
                if (uniqueRoot != null)
                {
                    familyTree.Add(BuildFamilyTreeNode(uniqueRoot, allMembers, memberDict));
                }
            }
            else
            {
                // 如果没有设置Generation的男性根节点，选择ID最小的男性作为根节点
                var uniqueRoot = maleRoots.OrderBy(m => m.Id).FirstOrDefault();
                
                if (uniqueRoot != null)
                {
                    familyTree.Add(BuildFamilyTreeNode(uniqueRoot, allMembers, memberDict));
                }
            }
        }
        else if (allMembers.Any())
        {
            // 如果没有男性根节点，选择ID最小的成员作为根节点
            var uniqueRoot = allMembers.OrderBy(m => m.Id).FirstOrDefault();
            
            if (uniqueRoot != null)
            {
                familyTree.Add(BuildFamilyTreeNode(uniqueRoot, allMembers, memberDict));
            }
        }
        
        return familyTree;
    }
    
    private object BuildFamilyTreeNode(FamilyMember member, List<FamilyMember> allMembers, Dictionary<int, FamilyMember> memberDict)
    {
        // 查找当前成员的子女
        var children = allMembers.Where(m => m.FatherId == member.Id || m.MotherId == member.Id).ToList();
        
        // 构建节点对象
            var node = new
            {
                id = member.Id,
                name = member.FullName,
                gender = member.GenderDisplay,
                generation = member.Generation,
                imageUrl = member.ImageUrl,
                spouse = member.Spouse != null ? new
                {
                    id = member.Spouse.Id,
                    name = member.Spouse.FullName,
                    gender = member.Spouse.GenderDisplay,
                    imageUrl = member.Spouse.ImageUrl
                } : null,
                children = children.Select(child => BuildFamilyTreeNode(child, allMembers, memberDict)).ToList()
            };
        
        return node;
    }
    
    /// <summary>
    /// 获取最新动态数据
    /// </summary>
    /// <returns>最新动态列表</returns>
    private List<LatestNewsViewModel> GetLatestNews()
    {
        var latestNews = new List<LatestNewsViewModel>();
        
        // 获取最新新增人员（最近10条）
        var latestMembers = _context.FamilyMembers
            .OrderByDescending(m => m.CreatedAt)
            .Take(5)
            .Select(m => new LatestNewsViewModel
            {
                Type = "新增人员",
                Title = $"{m.FullName} 加入家族",
                Description = $"第{m.Generation}代成员，性别：{m.GenderDisplay}",
                CreatedAt = m.CreatedAt,
                Icon = "👤",
                LinkUrl = Url.Action("Details", "FamilyMember", new { id = m.Id })
            })
            .ToList();
            
        // 获取最新祖训（最近5条）
        var latestRules = _context.FamilyRules
            .OrderByDescending(r => r.CreatedAt)
            .Take(3)
            .Select(r => new LatestNewsViewModel
            {
                Type = "祖训",
                Title = "新的祖训",
                Description = r.Content.Length > 50 ? r.Content.Substring(0, 50) + "..." : r.Content,
                CreatedAt = r.CreatedAt,
                Icon = "📜",
                LinkUrl = Url.Action("Index", "FamilyRule")
            })
            .ToList();
            
        // 获取最新宗族活动（最近5条）
        var latestActivities = _context.ClanActivities
            .OrderByDescending(a => a.CreatedAt)
            .Take(5)
            .Select(a => new LatestNewsViewModel
            {
                Type = "宗族活动",
                Title = a.Title,
                Description = a.Description.Length > 50 ? a.Description.Substring(0, 50) + "..." : a.Description,
                CreatedAt = a.CreatedAt,
                Icon = "🎉",
                LinkUrl = Url.Action("Index", "ClanActivity")
            })
            .ToList();
            
        // 合并所有动态并按时间倒序排列
        latestNews.AddRange(latestMembers);
        latestNews.AddRange(latestRules);
        latestNews.AddRange(latestActivities);
        
        return latestNews.OrderByDescending(n => n.CreatedAt).Take(10).ToList();
    }

    public IActionResult Privacy()
    {
        return View();
    }

    [ResponseCache(Duration = 0, Location = ResponseCacheLocation.None, NoStore = true)]
    public IActionResult Error()
    {
        return View(new ErrorViewModel { RequestId = Activity.Current?.Id ?? HttpContext.TraceIdentifier });
    }
}
