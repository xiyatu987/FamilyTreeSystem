using Microsoft.EntityFrameworkCore;
using FamilyTreeSystem.Models;
using FamilyTreeSystem.Models.Enums;

namespace FamilyTreeSystem.Data;

public class FamilyTreeContext : DbContext
{
    public DbSet<User> Users { get; set; }
    public DbSet<FamilyMember> FamilyMembers { get; set; }
    public DbSet<Ziwei> Ziwei { get; set; }
    public DbSet<Clan> Clans { get; set; }

    public DbSet<FamilyRule> FamilyRules { get; set; }
    public DbSet<ClanActivity> ClanActivities { get; set; }
    public DbSet<ChildRelation> ChildRelations { get; set; }
    public DbSet<SiblingRelation> SiblingRelations { get; set; }

    public FamilyTreeContext(DbContextOptions<FamilyTreeContext> options) : base(options)
    {}

    // 配置实体关系
    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        // 配置FamilyMember的自引用关系
        modelBuilder.Entity<FamilyMember>()
            .HasOne(fm => fm.Father)
            .WithMany()
            .HasForeignKey(fm => fm.FatherId)
            .OnDelete(DeleteBehavior.SetNull);

        modelBuilder.Entity<FamilyMember>()
            .HasOne(fm => fm.Mother)
            .WithMany()
            .HasForeignKey(fm => fm.MotherId)
            .OnDelete(DeleteBehavior.SetNull);

        // 配置FamilyMember的配偶关系

        modelBuilder.Entity<FamilyMember>()
            .HasOne(fm => fm.Spouse)
            .WithMany()
            .HasForeignKey(fm => fm.SpouseId)
            .OnDelete(DeleteBehavior.SetNull);



        // 配置ChildRelation关系
        modelBuilder.Entity<ChildRelation>()
            .HasOne(cr => cr.Parent)
            .WithMany(fm => fm.ChildRelations)
            .HasForeignKey(cr => cr.ParentId)
            .OnDelete(DeleteBehavior.Cascade);

        modelBuilder.Entity<ChildRelation>()
            .HasOne(cr => cr.Child)
            .WithMany()
            .HasForeignKey(cr => cr.ChildId)
            .OnDelete(DeleteBehavior.Cascade);

        // 配置SiblingRelation关系
        modelBuilder.Entity<SiblingRelation>()
            .HasOne(sr => sr.Member1)
            .WithMany(fm => fm.SiblingRelations1)
            .HasForeignKey(sr => sr.Member1Id)
            .OnDelete(DeleteBehavior.Cascade);

        modelBuilder.Entity<SiblingRelation>()
            .HasOne(sr => sr.Member2)
            .WithMany(fm => fm.SiblingRelations2)
            .HasForeignKey(sr => sr.Member2Id)
            .OnDelete(DeleteBehavior.Cascade);
    }
}